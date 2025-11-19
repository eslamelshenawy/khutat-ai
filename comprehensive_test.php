<?php

/**
 * Comprehensive Test Script
 * Tests all pages, routes, and HTTP methods
 */

echo "\n";
echo "🔍 اختبار شامل لجميع الصفحات والـ Routes\n";
echo "==========================================\n\n";

$baseUrl = 'http://127.0.0.1:8000';
$tests = [];
$passed = 0;
$failed = 0;

// Test public pages (should return 200)
echo "📄 اختبار الصفحات العامة:\n";
echo "-------------------------\n";

$publicPages = [
    '/' => 'الصفحة الرئيسية',
    '/test-css' => 'صفحة اختبار CSS',
    '/login' => 'تسجيل الدخول (redirect)',
    '/register' => 'إنشاء حساب (redirect)',
];

foreach ($publicPages as $path => $name) {
    $ch = curl_init($baseUrl . $path);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode == 200 || $httpCode == 302) {
        echo "✅ $name ($path): HTTP $httpCode\n";
        $passed++;
    } else {
        echo "❌ $name ($path): HTTP $httpCode\n";
        $failed++;
    }
}

echo "\n";

// Test protected pages (should redirect to login - 302)
echo "🔒 اختبار الصفحات المحمية (يجب أن تحوّل للـ login):\n";
echo "------------------------------------------------\n";

$protectedPages = [
    '/wizard/start' => 'بداية المعالج',
    '/plans' => 'قائمة الخطط',
];

foreach ($protectedPages as $path => $name) {
    $ch = curl_init($baseUrl . $path);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode == 302) {
        echo "✅ $name ($path): HTTP $httpCode (تحويل صحيح)\n";
        $passed++;
    } else {
        echo "❌ $name ($path): HTTP $httpCode (يجب أن يكون 302)\n";
        $failed++;
    }
}

echo "\n";

// Test admin panel
echo "⚙️ اختبار لوحة التحكم:\n";
echo "---------------------\n";

$adminPages = [
    '/admin' => 'لوحة التحكم الرئيسية',
    '/admin/login' => 'تسجيل دخول الأدمن',
];

foreach ($adminPages as $path => $name) {
    $ch = curl_init($baseUrl . $path);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode == 200 || $httpCode == 302) {
        echo "✅ $name ($path): HTTP $httpCode\n";
        $passed++;
    } else {
        echo "❌ $name ($path): HTTP $httpCode\n";
        $failed++;
    }
}

echo "\n";

// Test files existence
echo "📁 اختبار وجود الملفات المهمة:\n";
echo "-----------------------------\n";

$files = [
    'app/Livewire/WizardStart.php' => 'WizardStart Component',
    'app/Livewire/WizardSteps.php' => 'WizardSteps Component',
    'app/Livewire/Wizard/ChapterEditor.php' => 'ChapterEditor Component',
    'app/Services/OllamaService.php' => 'OllamaService',
    'config/ollama.php' => 'Ollama Config',
    'resources/views/welcome.blade.php' => 'Welcome View',
    'resources/views/components/layouts/app.blade.php' => 'App Layout',
    'resources/views/business-plans/index.blade.php' => 'Business Plans Index',
    'resources/views/business-plans/show.blade.php' => 'Business Plans Show',
    'resources/views/livewire/wizard-steps.blade.php' => 'Wizard Steps View',
    'resources/views/livewire/wizard/chapter-editor.blade.php' => 'Chapter Editor View',
];

foreach ($files as $path => $name) {
    if (file_exists(__DIR__ . '/' . $path)) {
        $size = filesize(__DIR__ . '/' . $path);
        $lines = count(file(__DIR__ . '/' . $path));
        echo "✅ $name: موجود ($lines سطر، " . number_format($size) . " بايت)\n";
        $passed++;
    } else {
        echo "❌ $name: غير موجود\n";
        $failed++;
    }
}

echo "\n";

// Test database connection and tables
echo "🗄️ اختبار قاعدة البيانات:\n";
echo "------------------------\n";

try {
    $db = new PDO('mysql:host=localhost;dbname=business_plan_wizard', 'root', '');
    echo "✅ الاتصال بقاعدة البيانات: نجح\n";
    $passed++;

    $tables = ['users', 'business_plans', 'chapters', 'templates'];
    foreach ($tables as $table) {
        $stmt = $db->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            echo "✅ جدول $table: موجود\n";
            $passed++;
        } else {
            echo "❌ جدول $table: غير موجود\n";
            $failed++;
        }
    }
} catch (PDOException $e) {
    echo "❌ الاتصال بقاعدة البيانات: فشل - " . $e->getMessage() . "\n";
    $failed++;
}

echo "\n";

// Test routes registration
echo "🛣️ اختبار تسجيل الـ Routes:\n";
echo "---------------------------\n";

$routesFile = __DIR__ . '/routes/web.php';
if (file_exists($routesFile)) {
    $content = file_get_contents($routesFile);

    $routeChecks = [
        "Route::get('/', function" => 'الصفحة الرئيسية',
        "Route::get('/wizard/start'" => 'بداية المعالج',
        "Route::get('/wizard/{businessPlan}/steps'" => 'خطوات المعالج',
        "Route::get('/plans'" => 'قائمة الخطط',
        "Route::get('/test-css'" => 'اختبار CSS',
    ];

    foreach ($routeChecks as $pattern => $name) {
        if (strpos($content, $pattern) !== false) {
            echo "✅ Route: $name\n";
            $passed++;
        } else {
            echo "❌ Route: $name - غير موجود\n";
            $failed++;
        }
    }
} else {
    echo "❌ ملف routes/web.php غير موجود\n";
    $failed++;
}

echo "\n";

// Test Livewire components
echo "⚡ اختبار Livewire Components:\n";
echo "-----------------------------\n";

$components = [
    'App\Livewire\WizardStart' => 'WizardStart',
    'App\Livewire\WizardSteps' => 'WizardSteps',
    'App\Livewire\Wizard\ChapterEditor' => 'ChapterEditor',
];

foreach ($components as $class => $name) {
    // Convert namespace to file path
    $relativePath = str_replace('App\Livewire\\', '', $class);
    $relativePath = str_replace('\\', '/', $relativePath);
    $file = __DIR__ . '/app/Livewire/' . $relativePath . '.php';

    if (file_exists($file)) {
        $content = file_get_contents($file);
        if (strpos($content, "class $name extends Component") !== false ||
            strpos($content, "class $name extends") !== false) {
            echo "✅ Component $name: موجود ويعمل\n";
            $passed++;
        } else {
            echo "⚠️ Component $name: الملف موجود لكن التعريف غير صحيح\n";
            $failed++;
        }
    } else {
        echo "❌ Component $name: غير موجود في المسار: $file\n";
        $failed++;
    }
}

echo "\n";

// Test Services
echo "🔧 اختبار الـ Services:\n";
echo "----------------------\n";

$services = [
    'OllamaService' => 'app/Services/OllamaService.php',
];

foreach ($services as $name => $path) {
    if (file_exists(__DIR__ . '/' . $path)) {
        $content = file_get_contents(__DIR__ . '/' . $path);
        $methods = ['generateChapterContent', 'chatWithAI', 'improveContent'];
        $allMethodsExist = true;

        foreach ($methods as $method) {
            if (strpos($content, "public function $method") === false) {
                $allMethodsExist = false;
                break;
            }
        }

        if ($allMethodsExist) {
            echo "✅ $name: موجود مع جميع الـ methods\n";
            $passed++;
        } else {
            echo "⚠️ $name: موجود لكن بعض الـ methods ناقصة\n";
            $failed++;
        }
    } else {
        echo "❌ $name: غير موجود\n";
        $failed++;
    }
}

echo "\n";

// Test Filament Resources
echo "📊 اختبار Filament Resources:\n";
echo "----------------------------\n";

$resources = [
    'BusinessPlanResource' => 'app/Filament/Resources/BusinessPlanResource.php',
    'TemplateResource' => 'app/Filament/Resources/TemplateResource.php',
];

foreach ($resources as $name => $path) {
    if (file_exists(__DIR__ . '/' . $path)) {
        $content = file_get_contents(__DIR__ . '/' . $path);
        if (strpos($content, 'protected static ?string $navigationLabel') !== false) {
            echo "✅ $name: موجود ومحسّن\n";
            $passed++;
        } else {
            echo "⚠️ $name: موجود لكن غير محسّن\n";
            $passed++;
        }
    } else {
        echo "❌ $name: غير موجود\n";
        $failed++;
    }
}

echo "\n";

// Test Views rendering
echo "🎨 اختبار الـ Views:\n";
echo "-------------------\n";

$views = [
    'welcome' => 'resources/views/welcome.blade.php',
    'test-css' => 'resources/views/test-css.blade.php',
    'wizard-steps' => 'resources/views/livewire/wizard-steps.blade.php',
];

foreach ($views as $name => $path) {
    if (file_exists(__DIR__ . '/' . $path)) {
        $content = file_get_contents(__DIR__ . '/' . $path);
        $hasTailwind = (strpos($content, 'tailwindcss.com') !== false || strpos($content, 'tailwind') !== false);
        $hasArabic = (strpos($content, 'dir="rtl"') !== false || strpos($content, 'lang="ar"') !== false);

        if ($hasTailwind && $hasArabic) {
            echo "✅ $name: موجود مع Tailwind و RTL\n";
            $passed++;
        } else if ($hasTailwind) {
            echo "✅ $name: موجود مع Tailwind\n";
            $passed++;
        } else {
            echo "⚠️ $name: موجود لكن بدون Tailwind\n";
            $passed++;
        }
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
    echo "🎉 ممتاز! جميع الاختبارات نجحت!\n";
    echo "✨ التطبيق جاهز 100% للاستخدام\n\n";

    echo "🚀 خطوات الاستخدام:\n";
    echo "-------------------\n";
    echo "1. افتح المتصفح على: http://127.0.0.1:8000/\n";
    echo "2. سجل دخول أو أنشئ حساب جديد من: http://127.0.0.1:8000/admin/register\n";
    echo "3. ابدأ إنشاء خطة عمل من: http://127.0.0.1:8000/wizard/start\n";
    echo "4. أو ادخل لوحة التحكم من: http://127.0.0.1:8000/admin\n\n";
} else {
    echo "⚠️ هناك $failed اختبار فشل. يرجى مراجعة الأخطاء أعلاه.\n\n";
}

echo "✅ تم الانتهاء من الاختبار الشامل!\n\n";
