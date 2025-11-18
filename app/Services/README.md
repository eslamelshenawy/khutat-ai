# OpenAI Service Documentation

## Overview

OpenAI Service provides comprehensive AI-powered features for the Business Plan Wizard application, including content generation, quality analysis, recommendations, and interactive chat capabilities.

## Features

### 1. Chapter Content Generation
Generate professional, contextual content for business plan chapters.

```php
use App\Services\OpenAIService;

$openAI = new OpenAIService();

$content = $openAI->generateChapterContent($chapter, [
    'target_market' => 'Small businesses in Saudi Arabia',
    'budget' => '100,000 SAR',
]);
```

**Supported Chapter Types:**
- `executive_summary` - Executive Summary (uses GPT-4)
- `company_description` - Company Description
- `market_analysis` - Market Analysis (uses GPT-4)
- `products_services` - Products & Services
- `marketing_strategy` - Marketing Strategy
- `operations_plan` - Operations Plan
- `management_team` - Management Team
- `financial_plan` - Financial Plan (uses GPT-4)
- `implementation_timeline` - Implementation Timeline
- `risk_analysis` - Risk Analysis

### 2. Business Plan Quality Analysis
Analyze and score business plans based on multiple criteria.

```php
$analysis = $openAI->analyzePlanQuality($businessPlan);

// Returns:
// [
//     'score' => 85,
//     'feedback' => 'Detailed analysis text...',
//     'analyzed_at' => '2025-11-18 12:00:00'
// ]
```

**Evaluation Criteria:**
- Clarity and Comprehensiveness (20 points)
- Content Quality and Details (20 points)
- Financial Feasibility and Projections (20 points)
- Market and Competitive Analysis (20 points)
- Implementation Plan and Realism (20 points)

### 3. Recommendations Generation
Generate actionable recommendations for business plan improvement.

```php
$recommendations = $openAI->generateRecommendations($businessPlan);

// Returns array of recommendations:
// [
//     [
//         'title' => 'Enhance Market Segmentation',
//         'description' => 'Consider dividing your market into...',
//         'priority' => 'high'
//     ],
//     ...
// ]
```

**Priority Levels:**
- `high` - Critical improvements
- `medium` - Important but not urgent
- `low` - Nice to have improvements

### 4. Content Improvement
Improve existing content based on specific instructions.

```php
$improvedContent = $openAI->improveContent(
    $originalContent,
    'Make it more persuasive and add statistical data'
);
```

### 5. AI Chat
Interactive chat for business plan guidance.

```php
$response = $openAI->chatWithAI(
    'How can I improve my marketing strategy?',
    [
        ['role' => 'user', 'content' => 'Previous message'],
        ['role' => 'assistant', 'content' => 'Previous response']
    ]
);
```

## Configuration

### Environment Variables

Add these to your `.env` file:

```env
# Required
OPENAI_API_KEY=sk-...

# Optional
OPENAI_ORGANIZATION=org-...
OPENAI_DEFAULT_MODEL=gpt-3.5-turbo
OPENAI_ADVANCED_MODEL=gpt-4-turbo-preview
OPENAI_RATE_LIMIT_PER_MINUTE=10
```

### Config File

Customize settings in `config/openai.php`:

```php
return [
    'models' => [
        'default' => 'gpt-3.5-turbo',
        'advanced' => 'gpt-4-turbo-preview',
    ],
    'rate_limit_per_minute' => 10,
    'cost_tracking' => [
        'enabled' => true,
        'monthly_limits' => [
            'free' => 5.00,
            'basic' => 20.00,
            'pro' => 100.00,
        ],
    ],
];
```

## Error Handling

The service includes comprehensive error handling:

```php
try {
    $content = $openAI->generateChapterContent($chapter);
} catch (Exception $e) {
    // Error message in Arabic
    echo $e->getMessage();
    // "فشل توليد محتوى الفصل: ..."
}
```

**Common Errors:**
- Rate limit exceeded
- Invalid API key
- Insufficient quota
- Network timeout

## Rate Limiting

Built-in rate limiting prevents API abuse:

- **Free Tier**: 10 requests/minute
- **Basic Tier**: Configurable
- **Pro Tier**: Configurable
- **Enterprise**: Unlimited

```php
// Rate limit is automatically checked
$openAI->generateChapterContent($chapter);
// Throws exception if limit exceeded
```

## Retry Logic

Automatic retry with exponential backoff:

- Maximum 3 retry attempts
- 2-second base delay
- Exponential backoff (2s, 4s, 6s)
- Skips retry for rate limit errors

## Cost Tracking

All API calls are logged with cost calculation:

```php
// Automatic cost calculation based on:
// - Tokens used
// - Model pricing
// - Stored in ai_generations table
```

**Pricing (per 1K tokens):**
- GPT-4 Turbo: $0.01
- GPT-3.5 Turbo: $0.002

## Logging

All operations are logged for monitoring:

```php
Log::info('Generating chapter content', [
    'chapter_id' => $chapter->id,
    'model' => 'gpt-4-turbo-preview',
]);
```

**Logged Events:**
- Generation requests
- API responses
- Errors and retries
- Rate limit hits
- Cost calculations

## Database Tracking

All AI generations are stored in `ai_generations` table:

```php
[
    'business_plan_id' => 123,
    'chapter_id' => 456,
    'user_id' => 789,
    'generation_type' => 'chapter',
    'prompt' => 'Write executive summary...',
    'response' => 'Generated content...',
    'model_used' => 'gpt-4-turbo-preview',
    'tokens_used' => 1500,
    'cost' => 0.015,
    'processing_time_ms' => 3500,
    'status' => 'completed',
]
```

## Best Practices

### 1. Use Appropriate Models
```php
// For important chapters, the service automatically uses GPT-4
// For general content, it uses GPT-3.5 Turbo
```

### 2. Provide Context
```php
$content = $openAI->generateChapterContent($chapter, [
    'target_market' => '...',
    'competitors' => '...',
    'unique_value' => '...',
]);
```

### 3. Handle Errors Gracefully
```php
try {
    $content = $openAI->generateChapterContent($chapter);
} catch (Exception $e) {
    Log::error($e);
    return response()->json(['error' => $e->getMessage()], 500);
}
```

### 4. Monitor Costs
```php
// Check monthly usage
$monthlyCost = AiGeneration::where('user_id', $userId)
    ->whereMonth('created_at', now()->month)
    ->sum('cost');
```

## Examples

### Complete Chapter Generation Workflow

```php
use App\Services\OpenAIService;
use App\Models\Chapter;

// 1. Get chapter
$chapter = Chapter::find($chapterId);

// 2. Prepare context
$context = [
    'industry_details' => 'E-commerce in Saudi Arabia',
    'target_audience' => 'Young professionals aged 25-40',
    'budget' => '500,000 SAR',
];

// 3. Generate content
$openAI = new OpenAIService();
$content = $openAI->generateChapterContent($chapter, $context);

// 4. Update chapter
$chapter->update([
    'content' => $content,
    'status' => 'ai_generated',
    'is_ai_generated' => true,
    'ai_generated_at' => now(),
]);
```

### Business Plan Analysis Workflow

```php
use App\Services\OpenAIService;
use App\Models\BusinessPlan;

// 1. Get business plan
$plan = BusinessPlan::with('chapters')->find($planId);

// 2. Analyze quality
$openAI = new OpenAIService();
$analysis = $openAI->analyzePlanQuality($plan);

// 3. Update plan with score
$plan->update([
    'ai_score' => $analysis['score'],
    'ai_feedback' => $analysis['feedback'],
    'last_analyzed_at' => now(),
]);

// 4. Generate recommendations
$recommendations = $openAI->generateRecommendations($plan);

// 5. Store recommendations
foreach ($recommendations as $rec) {
    $plan->aiRecommendations()->create([
        'title' => $rec['title'],
        'description' => $rec['description'],
        'priority' => $rec['priority'],
    ]);
}
```

## Testing

```php
use Tests\TestCase;
use App\Services\OpenAIService;
use App\Models\Chapter;

class OpenAIServiceTest extends TestCase
{
    public function test_generates_chapter_content()
    {
        $chapter = Chapter::factory()->create();
        $service = new OpenAIService();

        $content = $service->generateChapterContent($chapter);

        $this->assertNotEmpty($content);
        $this->assertIsString($content);
    }

    public function test_logs_generation_in_database()
    {
        $chapter = Chapter::factory()->create();
        $service = new OpenAIService();

        $service->generateChapterContent($chapter);

        $this->assertDatabaseHas('ai_generations', [
            'chapter_id' => $chapter->id,
            'generation_type' => 'chapter',
            'status' => 'completed',
        ]);
    }
}
```

## Troubleshooting

### Issue: Rate Limit Exceeded
**Solution**: Wait 1 minute or upgrade user tier

### Issue: Invalid API Key
**Solution**: Check `OPENAI_API_KEY` in `.env`

### Issue: Timeout
**Solution**: Increase `OPENAI_REQUEST_TIMEOUT` in `.env`

### Issue: High Costs
**Solution**:
- Use GPT-3.5 for non-critical content
- Implement caching
- Set monthly limits

## Support

For issues or questions:
1. Check the logs: `storage/logs/laravel.log`
2. Review `ai_generations` table for failed requests
3. Verify API key and quota on OpenAI dashboard

## Version History

- **v1.0.0** (2025-11-18): Initial release
  - Chapter content generation
  - Quality analysis
  - Recommendations
  - Content improvement
  - AI chat
  - Rate limiting
  - Cost tracking
  - Error handling
  - Retry logic
