<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\OpenAIService;
use App\Models\Chapter;
use App\Models\BusinessPlan;
use App\Models\User;
use App\Models\AiGeneration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;

class OpenAIServiceTest extends TestCase
{
    use RefreshDatabase;

    protected OpenAIService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new OpenAIService();
    }

    /** @test */
    public function it_can_be_instantiated()
    {
        $this->assertInstanceOf(OpenAIService::class, $this->service);
    }

    /** @test */
    public function it_selects_advanced_model_for_important_chapters()
    {
        $plan = BusinessPlan::factory()->create();

        // Important chapter
        $executiveSummary = Chapter::factory()->create([
            'business_plan_id' => $plan->id,
            'chapter_type' => 'executive_summary',
        ]);

        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('selectModel');
        $method->setAccessible(true);

        $model = $method->invoke($this->service, $executiveSummary);

        $this->assertEquals('gpt-4-turbo-preview', $model);
    }

    /** @test */
    public function it_selects_default_model_for_regular_chapters()
    {
        $plan = BusinessPlan::factory()->create();

        // Regular chapter
        $chapter = Chapter::factory()->create([
            'business_plan_id' => $plan->id,
            'chapter_type' => 'company_description',
        ]);

        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('selectModel');
        $method->setAccessible(true);

        $model = $method->invoke($this->service, $chapter);

        $this->assertEquals('gpt-3.5-turbo', $model);
    }

    /** @test */
    public function it_builds_chapter_prompt_correctly()
    {
        $plan = BusinessPlan::factory()->create([
            'company_name' => 'شركة الاختبار',
            'project_type' => 'تجاري',
            'industry_type' => 'تكنولوجيا',
            'vision' => 'رؤية الشركة',
            'mission' => 'رسالة الشركة',
        ]);

        $chapter = Chapter::factory()->create([
            'business_plan_id' => $plan->id,
            'title' => 'الملخص التنفيذي',
            'chapter_type' => 'executive_summary',
        ]);

        $context = [
            'السوق المستهدف' => 'الشباب',
            'الميزانية' => '100000',
        ];

        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('buildChapterPrompt');
        $method->setAccessible(true);

        $prompt = $method->invoke($this->service, $chapter, $context);

        $this->assertStringContainsString('شركة الاختبار', $prompt);
        $this->assertStringContainsString('تكنولوجيا', $prompt);
        $this->assertStringContainsString('رؤية الشركة', $prompt);
        $this->assertStringContainsString('الشباب', $prompt);
        $this->assertStringContainsString('100000', $prompt);
    }

    /** @test */
    public function it_prepares_plan_for_analysis_correctly()
    {
        $plan = BusinessPlan::factory()->create([
            'title' => 'خطة الاختبار',
            'company_name' => 'شركة الاختبار',
            'industry_type' => 'تقنية',
        ]);

        $chapter1 = Chapter::factory()->create([
            'business_plan_id' => $plan->id,
            'title' => 'الفصل الأول',
            'content' => 'محتوى الفصل الأول',
        ]);

        $chapter2 = Chapter::factory()->create([
            'business_plan_id' => $plan->id,
            'title' => 'الفصل الثاني',
            'content' => 'محتوى الفصل الثاني',
        ]);

        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('preparePlanForAnalysis');
        $method->setAccessible(true);

        $content = $method->invoke($this->service, $plan->fresh(['chapters']));

        $this->assertStringContainsString('خطة الاختبار', $content);
        $this->assertStringContainsString('شركة الاختبار', $content);
        $this->assertStringContainsString('الفصل الأول', $content);
        $this->assertStringContainsString('محتوى الفصل الأول', $content);
    }

    /** @test */
    public function it_calculates_cost_correctly()
    {
        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('calculateCost');
        $method->setAccessible(true);

        // GPT-4 Turbo: $0.01 per 1K tokens
        $cost = $method->invoke($this->service, 1000, 'gpt-4-turbo-preview');
        $this->assertEquals(0.01, $cost);

        // GPT-3.5 Turbo: $0.002 per 1K tokens
        $cost = $method->invoke($this->service, 1000, 'gpt-3.5-turbo');
        $this->assertEquals(0.002, $cost);

        // 2000 tokens
        $cost = $method->invoke($this->service, 2000, 'gpt-4-turbo-preview');
        $this->assertEquals(0.02, $cost);
    }

    /** @test */
    public function it_parses_analysis_response_with_score()
    {
        $response = "بعد تحليل الخطة، أعطيها تقييماً بـ 85/100 بناءً على المعايير التالية...";

        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('parseAnalysisResponse');
        $method->setAccessible(true);

        $result = $method->invoke($this->service, $response);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('score', $result);
        $this->assertArrayHasKey('feedback', $result);
        $this->assertArrayHasKey('analyzed_at', $result);
        $this->assertEquals(85, $result['score']);
    }

    /** @test */
    public function it_parses_analysis_response_without_score()
    {
        $response = "تحليل شامل للخطة بدون ذكر تقييم رقمي محدد.";

        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('parseAnalysisResponse');
        $method->setAccessible(true);

        $result = $method->invoke($this->service, $response);

        // Should default to 70 if no score found
        $this->assertEquals(70, $result['score']);
    }

    /** @test */
    public function it_parses_recommendations_correctly()
    {
        $response = "1. تحسين استراتيجية التسويق\nيجب التركيز على وسائل التواصل الاجتماعي\n\n2. زيادة رأس المال\nالحاجة لتمويل إضافي\n\n3. توظيف فريق متخصص\nهذه أولوية عالية";

        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('parseRecommendations');
        $method->setAccessible(true);

        $recommendations = $method->invoke($this->service, $response);

        $this->assertIsArray($recommendations);
        $this->assertGreaterThan(0, count($recommendations));
        $this->assertArrayHasKey('title', $recommendations[0]);
        $this->assertArrayHasKey('description', $recommendations[0]);
        $this->assertArrayHasKey('priority', $recommendations[0]);
    }

    /** @test */
    public function it_throws_exception_when_rate_limit_exceeded()
    {
        $userId = 1;
        $cacheKey = "openai_rate_limit:user:{$userId}";

        // Set cache to exceed limit
        Cache::put($cacheKey, 10, 60);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('تم تجاوز الحد المسموح');

        $this->service->handleRateLimit($userId);
    }

    /** @test */
    public function it_allows_request_when_under_rate_limit()
    {
        $userId = 1;
        $cacheKey = "openai_rate_limit:user:{$userId}";

        // Clear cache
        Cache::forget($cacheKey);

        // Should not throw exception
        $this->service->handleRateLimit($userId);

        // Check that counter was incremented
        $this->assertEquals(1, Cache::get($cacheKey));
    }

    /** @test */
    public function it_gets_chapter_specific_instructions()
    {
        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('getChapterSpecificInstructions');
        $method->setAccessible(true);

        $instructions = $method->invoke($this->service, 'executive_summary');
        $this->assertStringContainsString('ملخص', $instructions);

        $instructions = $method->invoke($this->service, 'market_analysis');
        $this->assertStringContainsString('سوق', $instructions);

        $instructions = $method->invoke($this->service, 'financial_plan');
        $this->assertStringContainsString('مال', $instructions);
    }

    /** @test */
    public function it_has_system_prompt()
    {
        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('getSystemPrompt');
        $method->setAccessible(true);

        $prompt = $method->invoke($this->service);

        $this->assertNotEmpty($prompt);
        $this->assertStringContainsString('مستشار', $prompt);
        $this->assertStringContainsString('احترافي', $prompt);
    }
}
