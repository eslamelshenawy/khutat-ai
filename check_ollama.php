<?php

/**
 * Check Ollama Status
 * فحص حالة Ollama
 */

echo "\n";
echo "🤖 فحص حالة Ollama\n";
echo "===================\n\n";

$ollamaUrl = 'http://localhost:11434';
$passed = 0;
$failed = 0;

// Test 1: Check if Ollama is running
echo "1️⃣ فحص تشغيل Ollama:\n";
echo "--------------------\n";

$ch = curl_init($ollamaUrl . '/api/version');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 2);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($httpCode == 200) {
    $version = json_decode($response, true);
    echo "✅ Ollama يعمل!\n";
    echo "   Version: " . ($version['version'] ?? 'unknown') . "\n\n";
    $passed++;
} else {
    echo "❌ Ollama غير شغال!\n";
    echo "   Error: $error\n\n";
    echo "🔧 الحل:\n";
    echo "   1. حمّل Ollama من: https://ollama.com/download\n";
    echo "   2. شغّل: ollama serve\n\n";
    $failed++;

    // Stop here if Ollama is not running
    echo "⚠️ يجب تشغيل Ollama أولاً قبل المتابعة.\n";
    echo "   راجع ملف OLLAMA_SETUP.md للتعليمات الكاملة.\n\n";
    exit(1);
}

// Test 2: Check available models
echo "2️⃣ فحص Models المثبتة:\n";
echo "---------------------\n";

$ch = curl_init($ollamaUrl . '/api/tags');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode == 200) {
    $data = json_decode($response, true);
    $models = $data['models'] ?? [];

    if (count($models) > 0) {
        echo "✅ تم العثور على " . count($models) . " model(s):\n";
        foreach ($models as $model) {
            $name = $model['name'] ?? 'unknown';
            $size = isset($model['size']) ? round($model['size'] / 1024 / 1024 / 1024, 2) . ' GB' : 'unknown';
            echo "   - $name ($size)\n";
        }
        echo "\n";
        $passed++;
    } else {
        echo "❌ لا توجد models مثبتة!\n\n";
        echo "🔧 الحل:\n";
        echo "   شغّل في CMD:\n";
        echo "   ollama pull gemma:2b\n\n";
        $failed++;
    }
} else {
    echo "❌ فشل الحصول على قائمة Models\n\n";
    $failed++;
}

// Test 3: Test generation with current config
echo "3️⃣ اختبار إعدادات التطبيق:\n";
echo "------------------------\n";

// Read config
$configFile = __DIR__ . '/config/ollama.php';
if (file_exists($configFile)) {
    include $configFile;
    $config = $config ?? [];

    $baseUrl = $config['base_url'] ?? 'http://localhost:11434';
    $model = $config['default_model'] ?? 'gemma:2b';

    echo "✅ ملف الإعدادات موجود\n";
    echo "   Base URL: $baseUrl\n";
    echo "   Default Model: $model\n\n";
    $passed++;

    // Check if the configured model exists
    if ($httpCode == 200 && count($models) > 0) {
        $modelExists = false;
        foreach ($models as $m) {
            if (($m['name'] ?? '') === $model) {
                $modelExists = true;
                break;
            }
        }

        if ($modelExists) {
            echo "✅ الـ Model المحدد ($model) مثبت\n\n";
            $passed++;
        } else {
            echo "❌ الـ Model المحدد ($model) غير مثبت!\n\n";
            echo "🔧 الحل:\n";
            echo "   شغّل في CMD:\n";
            echo "   ollama pull $model\n\n";
            $failed++;
        }
    }
} else {
    echo "❌ ملف الإعدادات غير موجود!\n\n";
    $failed++;
}

// Test 4: Test actual generation
echo "4️⃣ اختبار توليد محتوى:\n";
echo "-------------------\n";

if ($passed >= 3) {
    echo "جاري اختبار التوليد...\n";

    $testPrompt = "اكتب مقدمة قصيرة عن خطة عمل في 20 كلمة";

    $postData = json_encode([
        'model' => $model,
        'prompt' => $testPrompt,
        'stream' => false,
        'options' => [
            'temperature' => 0.7,
            'num_predict' => 50,
        ]
    ]);

    $ch = curl_init($ollamaUrl . '/api/generate');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    if ($httpCode == 200) {
        $result = json_decode($response, true);
        $generatedText = $result['response'] ?? '';

        if (!empty($generatedText)) {
            echo "✅ التوليد يعمل!\n";
            echo "   النص المولد: " . substr($generatedText, 0, 100) . "...\n\n";
            $passed++;
        } else {
            echo "❌ فشل التوليد (رد فارغ)\n\n";
            $failed++;
        }
    } else {
        echo "❌ فشل التوليد!\n";
        echo "   Error: $error\n\n";
        $failed++;
    }
} else {
    echo "⏭️ تم تخطي اختبار التوليد (إصلح المشاكل السابقة أولاً)\n\n";
}

// Final summary
echo "📊 الملخص النهائي:\n";
echo "=================\n";
echo "✅ اختبارات ناجحة: $passed\n";
echo "❌ اختبارات فاشلة: $failed\n\n";

if ($failed === 0) {
    echo "🎉 ممتاز! Ollama جاهز للاستخدام!\n";
    echo "✨ يمكنك الآن استخدام ميزة الذكاء الاصطناعي في التطبيق\n\n";

    echo "🚀 خطوات الاستخدام:\n";
    echo "-------------------\n";
    echo "1. افتح: http://127.0.0.1:8000/wizard/start\n";
    echo "2. أنشئ خطة عمل جديدة\n";
    echo "3. اضغط 'توليد بالذكاء الاصطناعي' في أي فصل\n\n";
} else {
    echo "⚠️ هناك $failed مشكلة. يرجى إصلاحها:\n\n";

    if ($failed == 1 && $passed == 0) {
        echo "📝 تعليمات التثبيت:\n";
        echo "-------------------\n";
        echo "1. حمّل Ollama من: https://ollama.com/download\n";
        echo "2. ثبّت Ollama\n";
        echo "3. شغّل في CMD: ollama serve\n";
        echo "4. في CMD جديد: ollama pull gemma:2b\n";
        echo "5. شغّل هذا السكريبت مرة أخرى\n\n";
    }

    echo "راجع ملف OLLAMA_SETUP.md للتعليمات التفصيلية\n\n";
}

echo "✅ تم الانتهاء من الفحص!\n\n";
