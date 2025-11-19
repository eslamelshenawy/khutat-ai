<?php

/**
 * UI Completion Verification Script
 * Verifies that all UI components are properly implemented
 */

echo "\n";
echo "🔍 التحقق من اكتمال واجهات المستخدم\n";
echo "=====================================\n\n";

$checks = [];
$passed = 0;
$failed = 0;

// Check 1: wizard-steps.blade.php exists and is complete
$wizardStepsPath = __DIR__ . '/resources/views/livewire/wizard-steps.blade.php';
if (file_exists($wizardStepsPath)) {
    $content = file_get_contents($wizardStepsPath);
    $lineCount = substr_count($content, "\n");

    if ($lineCount > 100 && strpos($content, 'Progress Bar') !== false && strpos($content, 'AI Generate Button') !== false) {
        $checks[] = "✅ wizard-steps.blade.php كامل ($lineCount سطر)";
        $passed++;
    } else {
        $checks[] = "❌ wizard-steps.blade.php غير مكتمل";
        $failed++;
    }
} else {
    $checks[] = "❌ wizard-steps.blade.php غير موجود";
    $failed++;
}

// Check 2: chapter-editor.blade.php exists and is complete
$chapterEditorPath = __DIR__ . '/resources/views/livewire/wizard/chapter-editor.blade.php';
if (file_exists($chapterEditorPath)) {
    $content = file_get_contents($chapterEditorPath);
    $lineCount = substr_count($content, "\n");

    if ($lineCount > 100 && strpos($content, 'AI Chat Panel') !== false && strpos($content, 'Content Editor') !== false) {
        $checks[] = "✅ chapter-editor.blade.php كامل ($lineCount سطر)";
        $passed++;
    } else {
        $checks[] = "❌ chapter-editor.blade.php غير مكتمل";
        $failed++;
    }
} else {
    $checks[] = "❌ chapter-editor.blade.php غير موجود";
    $failed++;
}

// Check 3: WizardSteps.php uses correct layout
$wizardStepsPhpPath = __DIR__ . '/app/Livewire/WizardSteps.php';
if (file_exists($wizardStepsPhpPath)) {
    $content = file_get_contents($wizardStepsPhpPath);

    if (strpos($content, "#[Layout('components.layouts.app')]") !== false) {
        $checks[] = "✅ WizardSteps.php يستخدم Layout الصحيح";
        $passed++;
    } else {
        $checks[] = "❌ WizardSteps.php يستخدم Layout خاطئ";
        $failed++;
    }
} else {
    $checks[] = "❌ WizardSteps.php غير موجود";
    $failed++;
}

// Check 4: ChapterEditor.php uses correct sort_order
$chapterEditorPhpPath = __DIR__ . '/app/Livewire/Wizard/ChapterEditor.php';
if (file_exists($chapterEditorPhpPath)) {
    $content = file_get_contents($chapterEditorPhpPath);

    if (strpos($content, "orderBy('sort_order')") !== false && strpos($content, "route('business-plans.show'") !== false) {
        $checks[] = "✅ ChapterEditor.php يستخدم sort_order والـ route الصحيح";
        $passed++;
    } else {
        $checks[] = "❌ ChapterEditor.php به مشاكل في الـ sort_order أو الـ route";
        $failed++;
    }
} else {
    $checks[] = "❌ ChapterEditor.php غير موجود";
    $failed++;
}

// Check 5: Routes file has correct imports
$routesPath = __DIR__ . '/routes/web.php';
if (file_exists($routesPath)) {
    $content = file_get_contents($routesPath);

    if (strpos($content, "use App\Livewire\Wizard\ChapterEditor;") !== false) {
        $checks[] = "✅ routes/web.php يستورد ChapterEditor بشكل صحيح";
        $passed++;
    } else {
        $checks[] = "❌ routes/web.php استيراد ChapterEditor خاطئ";
        $failed++;
    }
} else {
    $checks[] = "❌ routes/web.php غير موجود";
    $failed++;
}

// Check 6: OllamaService exists and is complete
$ollamaServicePath = __DIR__ . '/app/Services/OllamaService.php';
if (file_exists($ollamaServicePath)) {
    $content = file_get_contents($ollamaServicePath);

    if (strpos($content, 'generateChapterContent') !== false &&
        strpos($content, 'chatWithAI') !== false &&
        strpos($content, 'improveContent') !== false) {
        $checks[] = "✅ OllamaService كامل ويحتوي على جميع الدوال";
        $passed++;
    } else {
        $checks[] = "❌ OllamaService غير مكتمل";
        $failed++;
    }
} else {
    $checks[] = "❌ OllamaService غير موجود";
    $failed++;
}

// Check 7: Main app layout exists
$appLayoutPath = __DIR__ . '/resources/views/components/layouts/app.blade.php';
if (file_exists($appLayoutPath)) {
    $content = file_get_contents($appLayoutPath);

    if (strpos($content, '@livewireStyles') !== false && strpos($content, 'dir="rtl"') !== false) {
        $checks[] = "✅ components/layouts/app.blade.php موجود ويدعم RTL";
        $passed++;
    } else {
        $checks[] = "❌ components/layouts/app.blade.php غير مكتمل";
        $failed++;
    }
} else {
    $checks[] = "❌ components/layouts/app.blade.php غير موجود";
    $failed++;
}

// Check 8: Business plans views exist
$businessPlansIndexPath = __DIR__ . '/resources/views/business-plans/index.blade.php';
$businessPlansShowPath = __DIR__ . '/resources/views/business-plans/show.blade.php';

if (file_exists($businessPlansIndexPath) && file_exists($businessPlansShowPath)) {
    $checks[] = "✅ business-plans views موجودة (index & show)";
    $passed++;
} else {
    $checks[] = "❌ business-plans views غير كاملة";
    $failed++;
}

// Check 9: WizardSteps uses OllamaService
$wizardStepsPhpContent = file_get_contents($wizardStepsPhpPath);
if (strpos($wizardStepsPhpContent, 'new OllamaService()') !== false) {
    $checks[] = "✅ WizardSteps يستخدم OllamaService";
    $passed++;
} else {
    $checks[] = "❌ WizardSteps لا يستخدم OllamaService";
    $failed++;
}

// Check 10: Ollama config exists
$ollamaConfigPath = __DIR__ . '/config/ollama.php';
if (file_exists($ollamaConfigPath)) {
    $checks[] = "✅ config/ollama.php موجود";
    $passed++;
} else {
    $checks[] = "❌ config/ollama.php غير موجود";
    $failed++;
}

// Display results
echo "نتائج الاختبار:\n";
echo "----------------\n";
foreach ($checks as $check) {
    echo "$check\n";
}

echo "\n";
echo "📊 الملخص النهائي:\n";
echo "================\n";
echo "✅ اختبارات ناجحة: $passed\n";
echo "❌ اختبارات فاشلة: $failed\n";
echo "📈 نسبة النجاح: " . round(($passed / ($passed + $failed)) * 100) . "%\n\n";

if ($failed === 0) {
    echo "🎉 جميع واجهات المستخدم مكتملة وجاهزة للاستخدام!\n";
    echo "\nالخطوات التالية:\n";
    echo "1. قم بتشغيل السيرفر: php artisan serve\n";
    echo "2. قم بزيارة: http://127.0.0.1:8000/wizard/start\n";
    echo "3. أنشئ خطة عمل جديدة واختبر الواجهات\n";
    echo "4. جرّب ميزة الذكاء الاصطناعي (بعد تثبيت Ollama)\n\n";
} else {
    echo "⚠️  هناك بعض المشاكل التي تحتاج إلى إصلاح.\n\n";
}

echo "✨ تم الانتهاء من التحقق!\n\n";
