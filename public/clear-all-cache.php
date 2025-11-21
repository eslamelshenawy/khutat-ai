<?php
/**
 * Clear All Cache Script
 * WARNING: Delete this file after use for security!
 */

// Change directory to project root
chdir(__DIR__ . '/..');

// Load Composer autoloader
require __DIR__ . '/../vendor/autoload.php';

// Bootstrap Laravel application
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

echo "<html><head><meta charset='utf-8'><title>Cache Clear</title></head><body>";
echo "<h1>🧹 تنظيف الذاكرة المؤقتة</h1>";
echo "<pre>";

// Clear various caches
$commands = [
    'config:clear' => 'مسح cache الإعدادات',
    'route:clear' => 'مسح cache المسارات',
    'view:clear' => 'مسح cache العروض',
    'cache:clear' => 'مسح الذاكرة المؤقتة العامة',
    'optimize:clear' => 'مسح جميع أنواع الـ cache',
];

foreach ($commands as $command => $description) {
    echo "\n▶ $description...\n";
    try {
        $kernel->call($command);
        echo "✅ نجح\n";
    } catch (Exception $e) {
        echo "❌ فشل: " . $e->getMessage() . "\n";
    }
}

// Try to run composer dump-autoload
echo "\n▶ إعادة بناء autoload...\n";
$output = [];
$return_var = 0;
exec('composer dump-autoload 2>&1', $output, $return_var);
if ($return_var === 0) {
    echo "✅ نجح\n";
} else {
    echo "⚠ تحذير: " . implode("\n", $output) . "\n";
}

// Try Filament specific commands
echo "\n▶ مسح cache Filament...\n";
try {
    if (class_exists('Filament\Commands\ClearCachedComponentsCommand')) {
        $kernel->call('filament:optimize-clear');
        echo "✅ نجح\n";
    } else {
        echo "⚠ Filament optimize command غير متوفر\n";
    }
} catch (Exception $e) {
    echo "⚠ تحذير: " . $e->getMessage() . "\n";
}

echo "\n</pre>";
echo "<h2>✅ تم تنظيف جميع أنواع الـ cache بنجاح!</h2>";
echo "<p><strong style='color: red;'>⚠️ مهم جداً: احذف هذا الملف الآن لأسباب أمنية!</strong></p>";
echo "<p>الملف: <code>public/clear-all-cache.php</code></p>";
echo "<hr>";
echo "<p><a href='/admin'>↩ الذهاب للوحة الإدارة</a></p>";
echo "</body></html>";
