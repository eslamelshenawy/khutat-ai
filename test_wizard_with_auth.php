<?php

/**
 * Test Wizard Pages with Authentication
 */

echo "\n";
echo "🔐 اختبار صفحات المعالج مع Authentication\n";
echo "=========================================\n\n";

$baseUrl = 'http://127.0.0.1:8000';
$passed = 0;
$failed = 0;

// Test if we can access wizard pages
echo "📄 اختبار الوصول لصفحات المعالج:\n";
echo "--------------------------------\n";

// Test 1: Check if wizard/start redirects to login
$ch = curl_init($baseUrl . '/wizard/start');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
curl_setopt($ch, CURLOPT_NOBODY, true);
curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode == 302) {
    echo "✅ /wizard/start: يحول للـ login (HTTP $httpCode)\n";
    $passed++;
} else {
    echo "❌ /wizard/start: HTTP $httpCode (يجب أن يكون 302)\n";
    $failed++;
}

// Test 2: Check if wizard/{id}/steps redirects to login
$ch = curl_init($baseUrl . '/wizard/1/steps');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
curl_setopt($ch, CURLOPT_NOBODY, true);
curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode == 302) {
    echo "✅ /wizard/1/steps: يحول للـ login (HTTP $httpCode)\n";
    $passed++;
} else {
    echo "❌ /wizard/1/steps: HTTP $httpCode (يجب أن يكون 302)\n";
    $failed++;
}

// Test 3: Check if wizard/{id}/chapters redirects to login
$ch = curl_init($baseUrl . '/wizard/1/chapters');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
curl_setopt($ch, CURLOPT_NOBODY, true);
curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode == 302) {
    echo "✅ /wizard/1/chapters: يحول للـ login (HTTP $httpCode)\n";
    $passed++;
} else {
    echo "❌ /wizard/1/chapters: HTTP $httpCode (يجب أن يكون 302)\n";
    $failed++;
}

echo "\n";

// Test route parameter names
echo "🛣️ اختبار Route Parameters:\n";
echo "---------------------------\n";

// Check routes file for correct parameter names
$routesFile = __DIR__ . '/routes/web.php';
if (file_exists($routesFile)) {
    $content = file_get_contents($routesFile);

    if (strpos($content, '{businessPlan}') !== false) {
        echo "✅ Route parameter: {businessPlan} موجود\n";
        $passed++;
    } else {
        echo "❌ Route parameter: {businessPlan} غير موجود\n";
        $failed++;
    }

    // Check WizardSteps mount method
    $wizardStepsFile = __DIR__ . '/app/Livewire/WizardSteps.php';
    if (file_exists($wizardStepsFile)) {
        $wizardContent = file_get_contents($wizardStepsFile);

        if (strpos($wizardContent, 'mount($businessPlan)') !== false) {
            echo "✅ WizardSteps mount: يستخدم \$businessPlan\n";
            $passed++;
        } else {
            echo "❌ WizardSteps mount: لا يستخدم \$businessPlan\n";
            $failed++;
        }

        if (strpos($wizardContent, "route('business-plans.show'") !== false) {
            echo "✅ WizardSteps: يستخدم business-plans.show route\n";
            $passed++;
        } else {
            echo "⚠️ WizardSteps: لا يستخدم business-plans.show (قد يستخدم plans.show)\n";
            $failed++;
        }
    }

    // Check WizardStart redirect
    $wizardStartFile = __DIR__ . '/app/Livewire/WizardStart.php';
    if (file_exists($wizardStartFile)) {
        $startContent = file_get_contents($wizardStartFile);

        if (strpos($startContent, "'businessPlan' =>") !== false ||
            strpos($startContent, "['businessPlan' =>") !== false ||
            strpos($startContent, '["businessPlan" =>') !== false) {
            echo "✅ WizardStart redirect: يستخدم businessPlan parameter\n";
            $passed++;
        } else {
            echo "❌ WizardStart redirect: لا يستخدم businessPlan parameter\n";
            $failed++;
        }
    }
}

echo "\n";

// Test Livewire components
echo "⚡ اختبار Livewire Components:\n";
echo "-----------------------------\n";

$components = [
    'app/Livewire/WizardStart.php' => 'WizardStart',
    'app/Livewire/WizardSteps.php' => 'WizardSteps',
    'app/Livewire/Wizard/ChapterEditor.php' => 'ChapterEditor',
];

foreach ($components as $path => $name) {
    if (file_exists(__DIR__ . '/' . $path)) {
        echo "✅ $name: موجود\n";
        $passed++;
    } else {
        echo "❌ $name: غير موجود\n";
        $failed++;
    }
}

echo "\n";

// Final summary
echo "📊 الملخص النهائي:\n";
echo "=================\n";
echo "✅ اختبارات ناجحة: $passed\n";
echo "❌ اختبارات فاشلة: $failed\n";
$total = $passed + $failed;
$percentage = $total > 0 ? round(($passed / $total) * 100) : 0;
echo "📈 نسبة النجاح: $percentage%\n\n";

if ($failed === 0) {
    echo "🎉 ممتاز! جميع صفحات المعالج محمية ومضبوطة!\n";
    echo "✨ Route parameters صحيحة\n";
    echo "🔐 Authentication يعمل بشكل صحيح\n\n";

    echo "📝 للاختبار الكامل:\n";
    echo "-------------------\n";
    echo "1. سجل دخول من: http://127.0.0.1:8000/admin/login\n";
    echo "2. أنشئ خطة عمل من: http://127.0.0.1:8000/wizard/start\n";
    echo "3. ستنتقل تلقائياً لـ: http://127.0.0.1:8000/wizard/{id}/steps\n\n";
} else {
    echo "⚠️ هناك $failed اختبار فشل. يرجى مراجعة الأخطاء أعلاه.\n\n";
}

echo "✅ تم الانتهاء من الاختبار!\n\n";
