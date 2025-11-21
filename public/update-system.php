<?php
/**
 * System Update & Cache Clear Script
 * Run this after pulling new changes
 * WARNING: Delete this file after use!
 */

// Security check - change this password
define('UPDATE_PASSWORD', 'admin123');

if (!isset($_GET['pass']) || $_GET['pass'] !== UPDATE_PASSWORD) {
    die('Access Denied - Invalid Password');
}

// Change to project root
chdir(__DIR__ . '/..');

echo "<html><head><meta charset='utf-8'><title>System Update</title>";
echo "<style>body{font-family:Arial;max-width:800px;margin:50px auto;padding:20px;}";
echo "h1{color:#2563eb;}pre{background:#f3f4f6;padding:15px;border-radius:8px;overflow-x:auto;}";
echo ".success{color:#059669;}.error{color:#dc2626;}.warning{color:#d97706;}</style></head><body>";
echo "<h1>🚀 تحديث النظام ومسح الذاكرة المؤقتة</h1>";

// Step 1: Git Pull
echo "<h2>📥 الخطوة 1: سحب التحديثات من GitHub</h2><pre>";
$output = [];
$return = 0;
exec('git pull origin main 2>&1', $output, $return);
foreach ($output as $line) {
    echo htmlspecialchars($line) . "\n";
}
if ($return === 0 || strpos(implode('', $output), 'Already up to date') !== false) {
    echo "<span class='success'>✅ تم سحب التحديثات بنجاح</span>";
} else {
    echo "<span class='error'>❌ فشل سحب التحديثات</span>";
}
echo "</pre>";

// Step 2: Composer dump-autoload
echo "<h2>🔄 الخطوة 2: إعادة بناء Autoload</h2><pre>";
$output = [];
exec('composer dump-autoload -o 2>&1', $output, $return);
foreach ($output as $line) {
    echo htmlspecialchars($line) . "\n";
}
if ($return === 0) {
    echo "<span class='success'>✅ تم إعادة بناء Autoload بنجاح</span>";
} else {
    echo "<span class='warning'>⚠️ تحذير في Composer</span>";
}
echo "</pre>";

// Step 3: Clear Laravel Cache
echo "<h2>🧹 الخطوة 3: مسح ذاكرة Laravel المؤقتة</h2><pre>";
try {
    require __DIR__ . '/../vendor/autoload.php';
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

    $commands = [
        'config:clear',
        'route:clear',
        'view:clear',
        'cache:clear',
        'optimize:clear'
    ];

    foreach ($commands as $cmd) {
        echo "▶ تشغيل: {$cmd}\n";
        $kernel->call($cmd);
        echo "<span class='success'>✅ نجح</span>\n\n";
    }
} catch (Exception $e) {
    echo "<span class='error'>❌ خطأ: " . htmlspecialchars($e->getMessage()) . "</span>\n";
}
echo "</pre>";

// Step 4: Check if new files exist
echo "<h2>🔍 الخطوة 4: التحقق من الملفات الجديدة</h2><pre>";
$files = [
    'app/Filament/Resources/UserResource.php',
    'app/Filament/Resources/NotificationResource.php',
    'app/Filament/Resources/UserResource/Pages/ListUsers.php',
    'app/Filament/Resources/NotificationResource/Pages/ListNotifications.php'
];

foreach ($files as $file) {
    $exists = file_exists(__DIR__ . '/../' . $file);
    $status = $exists ? "<span class='success'>✅ موجود</span>" : "<span class='error'>❌ غير موجود</span>";
    echo "{$status} - {$file}\n";
}
echo "</pre>";

// Step 5: Check current git commit
echo "<h2>📝 الخطوة 5: معلومات Git الحالية</h2><pre>";
$output = [];
exec('git log -1 --oneline 2>&1', $output);
echo "آخر commit: " . htmlspecialchars(implode("\n", $output)) . "\n";

$output = [];
exec('git status -s 2>&1', $output);
if (empty($output)) {
    echo "<span class='success'>✅ لا توجد تغييرات غير محفوظة</span>\n";
} else {
    echo "<span class='warning'>⚠️ هناك تغييرات غير محفوظة:</span>\n";
    foreach ($output as $line) {
        echo htmlspecialchars($line) . "\n";
    }
}
echo "</pre>";

echo "<hr>";
echo "<h2>✅ اكتمل التحديث!</h2>";
echo "<p><strong style='color:red;'>⚠️ مهم جداً: احذف هذا الملف الآن!</strong></p>";
echo "<p>المسار: <code>public/update-system.php</code></p>";
echo "<hr>";
echo "<p><a href='/admin' style='background:#2563eb;color:white;padding:10px 20px;text-decoration:none;border-radius:5px;'>↩ الذهاب للوحة الإدارة</a></p>";
echo "</body></html>";
