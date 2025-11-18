# OpenAI Service - أمثلة الاستخدام

## نظرة عامة

هذا الملف يحتوي على أمثلة عملية لاستخدام OpenAI Service في تطبيق Business Plan Wizard.

---

## 1. توليد محتوى فصل - Executive Summary

### مثال أساسي

```php
use App\Services\OpenAIService;
use App\Models\Chapter;

// جلب الفصل
$chapter = Chapter::where('chapter_type', 'executive_summary')
    ->where('business_plan_id', $planId)
    ->first();

// إنشاء instance من الخدمة
$openAI = new OpenAIService();

// توليد المحتوى
try {
    $content = $openAI->generateChapterContent($chapter);

    // حفظ المحتوى
    $chapter->update([
        'content' => $content,
        'status' => 'ai_generated',
        'is_ai_generated' => true,
        'ai_generated_at' => now(),
    ]);

    echo "تم توليد المحتوى بنجاح!";

} catch (Exception $e) {
    echo "خطأ: " . $e->getMessage();
}
```

### مثال مع سياق إضافي

```php
$context = [
    'السوق المستهدف' => 'الشباب من 18-35 سنة في السعودية',
    'الميزة التنافسية' => 'سرعة التوصيل خلال ساعة واحدة',
    'حجم الاستثمار المطلوب' => '500,000 ريال سعودي',
    'نقطة التعادل المتوقعة' => 'بعد 18 شهر',
];

$content = $openAI->generateChapterContent($chapter, $context);
```

---

## 2. توليد محتوى لأنواع مختلفة من Business Plans

### خطة عمل لمطعم

```php
$restaurantChapter = Chapter::find($id);

$context = [
    'نوع المطعم' => 'مطعم وجبات سريعة صحية',
    'الموقع' => 'الرياض - حي النرجس',
    'السعة' => '50 مقعد',
    'ساعات العمل' => 'من 11 صباحاً حتى 11 مساءً',
    'العملاء المستهدفون' => 'موظفون وطلاب يهتمون بالصحة',
    'متوسط السعر' => '35-50 ريال للوجبة',
];

$content = $openAI->generateChapterContent($restaurantChapter, $context);
```

### خطة عمل لمتجر إلكتروني

```php
$ecommerceChapter = Chapter::find($id);

$context = [
    'نوع المتجر' => 'متجر إلكتروني لبيع الملابس الرياضية',
    'المنصة' => 'موقع ويب + تطبيق موبايل',
    'الموردون' => 'شركات محلية وعالمية',
    'طرق الدفع' => 'مدى، فيزا، ماستركارد، Apple Pay',
    'مناطق التوصيل' => 'جميع مدن المملكة',
    'المنافسون الرئيسيون' => 'نون، أمازون، سبورتس سيتي',
];

$content = $openAI->generateChapterContent($ecommerceChapter, $context);
```

### خطة عمل لتطبيق تقني

```php
$techChapter = Chapter::find($id);

$context = [
    'نوع التطبيق' => 'تطبيق لحجز الملاعب الرياضية',
    'المنصات' => 'iOS و Android',
    'الميزات الرئيسية' => 'حجز فوري، دفع إلكتروني، تقييمات، برنامج ولاء',
    'نموذج الإيرادات' => 'عمولة 15% على كل حجز',
    'عدد الملاعب المستهدفة' => '100 ملعب في السنة الأولى',
    'عدد المستخدمين المتوقع' => '10,000 مستخدم في السنة الأولى',
];

$content = $openAI->generateChapterContent($techChapter, $context);
```

---

## 3. تحليل جودة الخطة

### تحليل خطة كاملة

```php
use App\Models\BusinessPlan;

$plan = BusinessPlan::with('chapters')->find($planId);

try {
    $analysis = $openAI->analyzePlanQuality($plan);

    // عرض النتائج
    echo "التقييم الإجمالي: " . $analysis['score'] . "/100\n";
    echo "التحليل التفصيلي:\n" . $analysis['feedback'];

    // حفظ في قاعدة البيانات
    $plan->update([
        'ai_score' => $analysis['score'],
        'ai_feedback' => $analysis['feedback'],
        'last_analyzed_at' => $analysis['analyzed_at'],
    ]);

} catch (Exception $e) {
    echo "فشل التحليل: " . $e->getMessage();
}
```

### معالجة نتائج التحليل

```php
$analysis = $openAI->analyzePlanQuality($plan);

if ($analysis['score'] >= 80) {
    // خطة ممتازة
    notify($plan->user, 'خطتك ممتازة! جاهزة للعرض على المستثمرين');
} elseif ($analysis['score'] >= 60) {
    // خطة جيدة لكن تحتاج تحسينات
    notify($plan->user, 'خطتك جيدة، لكن هناك بعض التحسينات المقترحة');
} else {
    // خطة تحتاج عمل كبير
    notify($plan->user, 'خطتك تحتاج مراجعة شاملة');
}
```

---

## 4. توليد التوصيات

### الحصول على توصيات

```php
try {
    $recommendations = $openAI->generateRecommendations($plan);

    // حفظ التوصيات
    foreach ($recommendations as $recommendation) {
        $plan->aiRecommendations()->create([
            'title' => $recommendation['title'],
            'description' => $recommendation['description'],
            'priority' => $recommendation['priority'],
            'status' => 'pending',
        ]);
    }

    echo "تم توليد " . count($recommendations) . " توصيات";

} catch (Exception $e) {
    echo "فشل توليد التوصيات: " . $e->getMessage();
}
```

### عرض التوصيات حسب الأولوية

```php
$recommendations = $openAI->generateRecommendations($plan);

// تصنيف التوصيات
$highPriority = [];
$mediumPriority = [];
$lowPriority = [];

foreach ($recommendations as $rec) {
    switch ($rec['priority']) {
        case 'high':
            $highPriority[] = $rec;
            break;
        case 'medium':
            $mediumPriority[] = $rec;
            break;
        case 'low':
            $lowPriority[] = $rec;
            break;
    }
}

echo "توصيات ذات أولوية عالية: " . count($highPriority) . "\n";
echo "توصيات ذات أولوية متوسطة: " . count($mediumPriority) . "\n";
echo "توصيات ذات أولوية منخفضة: " . count($lowPriority) . "\n";
```

---

## 5. تحسين المحتوى

### تحسين محتوى فصل

```php
$currentContent = $chapter->content;

$instructions = "اجعل المحتوى أكثر إقناعاً وأضف بيانات إحصائية وأمثلة عملية";

try {
    $improvedContent = $openAI->improveContent($currentContent, $instructions);

    // حفظ المحتوى المحسّن
    $chapter->update([
        'content' => $improvedContent,
    ]);

    echo "تم تحسين المحتوى بنجاح!";

} catch (Exception $e) {
    echo "فشل التحسين: " . $e->getMessage();
}
```

### أمثلة تعليمات تحسين مختلفة

```php
// 1. جعل المحتوى أكثر احترافية
$improved = $openAI->improveContent(
    $content,
    "اجعل اللغة أكثر احترافية ورسمية"
);

// 2. تبسيط المحتوى
$simplified = $openAI->improveContent(
    $content,
    "بسّط اللغة لتكون مفهومة للجميع"
);

// 3. إضافة تفاصيل
$detailed = $openAI->improveContent(
    $content,
    "أضف تفاصيل أكثر وأمثلة توضيحية"
);

// 4. اختصار المحتوى
$shorter = $openAI->improveContent(
    $content,
    "اختصر المحتوى مع الحفاظ على النقاط الرئيسية"
);
```

---

## 6. الدردشة مع الذكاء الاصطناعي

### دردشة بسيطة

```php
try {
    $response = $openAI->chatWithAI(
        "كيف يمكنني تحسين استراتيجية التسويق لمشروعي؟"
    );

    echo "رد الذكاء الاصطناعي: " . $response;

} catch (Exception $e) {
    echo "خطأ: " . $e->getMessage();
}
```

### دردشة مع سياق المحادثة السابقة

```php
use App\Models\ChatMessage;

// جلب آخر 5 رسائل
$previousMessages = ChatMessage::where('business_plan_id', $planId)
    ->latest()
    ->take(5)
    ->get()
    ->reverse()
    ->map(function ($msg) {
        return [
            'role' => $msg->is_user ? 'user' : 'assistant',
            'content' => $msg->message
        ];
    })
    ->toArray();

// إرسال رسالة جديدة مع السياق
$response = $openAI->chatWithAI(
    "وما رأيك في استخدام إعلانات سناب شات؟",
    $previousMessages
);

// حفظ الرسائل
ChatMessage::create([
    'business_plan_id' => $planId,
    'user_id' => auth()->id(),
    'message' => "وما رأيك في استخدام إعلانات سناب شات؟",
    'is_user' => true,
]);

ChatMessage::create([
    'business_plan_id' => $planId,
    'user_id' => auth()->id(),
    'message' => $response,
    'is_user' => false,
]);
```

---

## 7. معالجة الأخطاء المتقدمة

### معالجة أخطاء Rate Limit

```php
use Illuminate\Support\Facades\Cache;

$userId = auth()->id();
$cacheKey = "openai_rate_limit:user:{$userId}";

// التحقق قبل الطلب
$currentCount = Cache::get($cacheKey, 0);
$limit = 10; // 10 requests per minute

if ($currentCount >= $limit) {
    return response()->json([
        'error' => 'لقد تجاوزت الحد المسموح. يرجى المحاولة بعد دقيقة.',
        'retry_after' => 60,
    ], 429);
}

try {
    $content = $openAI->generateChapterContent($chapter);
    return response()->json(['content' => $content]);
} catch (Exception $e) {
    return response()->json(['error' => $e->getMessage()], 500);
}
```

### Retry مخصص

```php
$maxAttempts = 3;
$content = null;

for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
    try {
        $content = $openAI->generateChapterContent($chapter);
        break; // نجح، اخرج من الحلقة
    } catch (Exception $e) {
        if ($attempt === $maxAttempts) {
            // فشلت جميع المحاولات
            throw $e;
        }

        // انتظر قبل المحاولة التالية
        sleep(2 * $attempt);
    }
}
```

---

## 8. مراقبة التكاليف

### حساب التكلفة الشهرية للمستخدم

```php
use App\Models\AiGeneration;

$userId = auth()->id();

$monthlyCost = AiGeneration::where('user_id', $userId)
    ->whereMonth('created_at', now()->month)
    ->whereYear('created_at', now()->year)
    ->sum('cost');

echo "التكلفة الشهرية: $" . number_format($monthlyCost, 2);

// التحقق من الحد المسموح
$limits = [
    'free' => 5.00,
    'basic' => 20.00,
    'pro' => 100.00,
];

$userTier = auth()->user()->subscription_tier ?? 'free';
$limit = $limits[$userTier];

if ($monthlyCost >= $limit) {
    echo "لقد وصلت إلى الحد المسموح. يرجى الترقية.";
}
```

### تقرير استخدام تفصيلي

```php
$usageReport = AiGeneration::where('user_id', $userId)
    ->whereMonth('created_at', now()->month)
    ->selectRaw('
        generation_type,
        COUNT(*) as count,
        SUM(tokens_used) as total_tokens,
        SUM(cost) as total_cost,
        AVG(processing_time_ms) as avg_processing_time
    ')
    ->groupBy('generation_type')
    ->get();

foreach ($usageReport as $row) {
    echo "{$row->generation_type}:\n";
    echo "  - العدد: {$row->count}\n";
    echo "  - Tokens: {$row->total_tokens}\n";
    echo "  - التكلفة: $" . number_format($row->total_cost, 3) . "\n";
    echo "  - متوسط الوقت: {$row->avg_processing_time}ms\n\n";
}
```

---

## 9. استخدام في Controllers

### مثال Controller للتوليد

```php
namespace App\Http\Controllers;

use App\Services\OpenAIService;
use App\Models\Chapter;
use Illuminate\Http\Request;

class ChapterGenerationController extends Controller
{
    protected OpenAIService $openAI;

    public function __construct(OpenAIService $openAI)
    {
        $this->openAI = $openAI;
    }

    public function generate(Request $request, Chapter $chapter)
    {
        // التحقق من الصلاحيات
        $this->authorize('update', $chapter->businessPlan);

        try {
            // التحقق من Rate Limit
            $this->openAI->handleRateLimit(auth()->id());

            // توليد المحتوى
            $content = $this->openAI->generateChapterContent(
                $chapter,
                $request->input('context', [])
            );

            // تحديث الفصل
            $chapter->update([
                'content' => $content,
                'status' => 'ai_generated',
                'is_ai_generated' => true,
                'ai_generated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'content' => $content,
                'message' => 'تم توليد المحتوى بنجاح',
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
```

---

## 10. استخدام في Jobs (Queue)

### مثال Job للتوليد المؤجل

```php
namespace App\Jobs;

use App\Services\OpenAIService;
use App\Models\Chapter;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class GenerateChapterContentJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public Chapter $chapter;
    public array $context;

    public function __construct(Chapter $chapter, array $context = [])
    {
        $this->chapter = $chapter;
        $this->context = $context;
    }

    public function handle(OpenAIService $openAI)
    {
        try {
            $content = $openAI->generateChapterContent($this->chapter, $this->context);

            $this->chapter->update([
                'content' => $content,
                'status' => 'ai_generated',
                'is_ai_generated' => true,
                'ai_generated_at' => now(),
            ]);

            // إرسال إشعار للمستخدم
            $this->chapter->businessPlan->user->notify(
                new ChapterGeneratedNotification($this->chapter)
            );

        } catch (Exception $e) {
            // تسجيل الخطأ
            \Log::error('Chapter generation failed', [
                'chapter_id' => $this->chapter->id,
                'error' => $e->getMessage(),
            ]);

            // إعادة المحاولة
            $this->release(60); // retry after 1 minute
        }
    }
}
```

### استدعاء الـ Job

```php
use App\Jobs\GenerateChapterContentJob;

// توليد فوري
GenerateChapterContentJob::dispatch($chapter, $context);

// توليد مؤجل
GenerateChapterContentJob::dispatch($chapter, $context)
    ->delay(now()->addMinutes(5));

// توليد على Queue محدد
GenerateChapterContentJob::dispatch($chapter, $context)
    ->onQueue('ai-generation');
```

---

## ملاحظات مهمة

### 1. الأمان
- لا تشارك `OPENAI_API_KEY` أبداً
- استخدم Rate Limiting لمنع الإساءة
- تحقق من صلاحيات المستخدم قبل التوليد

### 2. الأداء
- استخدم Queues للعمليات الطويلة
- فعّل Caching للطلبات المتشابهة
- راقب استهلاك الـ Tokens

### 3. التكاليف
- راقب التكاليف الشهرية
- ضع حدود للمستخدمين
- استخدم GPT-3.5 للمحتوى العادي

### 4. الجودة
- وفر سياق كافٍ للحصول على نتائج أفضل
- راجع المحتوى المولد قبل الحفظ
- استخدم GPT-4 للفصول المهمة

---

## الدعم

للمزيد من المعلومات، راجع:
- `app/Services/README.md` - التوثيق الكامل
- `config/openai.php` - الإعدادات
- [OpenAI API Documentation](https://platform.openai.com/docs)
