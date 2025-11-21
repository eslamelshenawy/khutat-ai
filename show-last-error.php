<?php

/**
 * Quick error checker - upload to server root and access via browser
 * URL: https://start.al-investor.com/show-last-error.php
 */

// Security: Only allow from localhost or specific IPs (optional)
// Uncomment the lines below for security
// $allowed_ips = ['127.0.0.1', 'YOUR_IP_HERE'];
// if (!in_array($_SERVER['REMOTE_ADDR'], $allowed_ips)) {
//     die('Access denied');
// }

?>
<!DOCTYPE html>
<html dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>Laravel Error Checker</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h1 { color: #333; border-bottom: 2px solid #e74c3c; padding-bottom: 10px; }
        h2 { color: #e74c3c; margin-top: 20px; }
        .success { color: #27ae60; }
        .error { color: #e74c3c; }
        pre { background: #2d3748; color: #f7fafc; padding: 15px; border-radius: 4px; overflow-x: auto; }
        .info { background: #e8f4fd; padding: 10px; border-left: 4px solid #3498db; margin: 10px 0; }
        .warning { background: #fff3cd; padding: 10px; border-left: 4px solid #ffc107; margin: 10px 0; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th, td { padding: 8px; text-align: right; border: 1px solid #ddd; }
        th { background: #f8f9fa; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Laravel Error Checker</h1>

        <?php
        $baseDir = __DIR__;

        // Check 1: Git Status
        echo "<h2>1. Git Status</h2>";
        if (is_dir($baseDir . '/.git')) {
            $lastCommit = trim(shell_exec('cd ' . $baseDir . ' && git log -1 --format="%h - %s (%cr)" 2>&1'));
            echo "<div class='info'>آخر commit: <strong>{$lastCommit}</strong></div>";

            $branch = trim(shell_exec('cd ' . $baseDir . ' && git branch --show-current 2>&1'));
            echo "<div class='info'>Branch: <strong>{$branch}</strong></div>";
        } else {
            echo "<div class='warning'>Not a git repository</div>";
        }

        // Check 2: Controller File Status
        echo "<h2>2. BusinessPlanTranslationController Status</h2>";
        $controllerPath = $baseDir . '/app/Http/Controllers/BusinessPlanTranslationController.php';
        if (file_exists($controllerPath)) {
            $content = file_get_contents($controllerPath);

            echo "<table>";
            echo "<tr><th>Check</th><th>Status</th></tr>";

            // Check for Str import
            if (strpos($content, 'use Illuminate\Support\Str;') !== false) {
                echo "<tr><td>Str import</td><td class='success'>✓ Found</td></tr>";
            } else {
                echo "<tr><td>Str import</td><td class='error'>✗ MISSING - Need git pull!</td></tr>";
            }

            // Check for old slug()
            if (strpos($content, 'slug($title)') !== false) {
                echo "<tr><td>Old slug() usage</td><td class='error'>✗ Found - Need git pull!</td></tr>";
            } else {
                echo "<tr><td>Old slug() usage</td><td class='success'>✓ Not found</td></tr>";
            }

            // Check for new Str::slug()
            if (strpos($content, 'Str::slug($title)') !== false) {
                echo "<tr><td>New Str::slug() usage</td><td class='success'>✓ Found</td></tr>";
            } else {
                echo "<tr><td>New Str::slug() usage</td><td class='error'>✗ NOT found</td></tr>";
            }

            echo "</table>";

            $lastModified = date('Y-m-d H:i:s', filemtime($controllerPath));
            echo "<div class='info'>Last modified: {$lastModified}</div>";
        } else {
            echo "<div class='error'>Controller file not found!</div>";
        }

        // Check 3: Laravel Log Files
        echo "<h2>3. Recent Laravel Errors</h2>";
        $logPath = $baseDir . '/storage/logs/laravel.log';
        if (file_exists($logPath)) {
            $logSize = filesize($logPath);
            echo "<div class='info'>Log file size: " . number_format($logSize / 1024, 2) . " KB</div>";

            // Get last 50 lines
            $lines = array_slice(file($logPath), -100);
            $errors = [];

            $currentError = '';
            foreach ($lines as $line) {
                if (preg_match('/\[\d{4}-\d{2}-\d{2}/', $line)) {
                    if ($currentError) {
                        $errors[] = $currentError;
                    }
                    $currentError = $line;
                } else {
                    $currentError .= $line;
                }
            }
            if ($currentError) {
                $errors[] = $currentError;
            }

            // Show last 3 errors
            $errors = array_slice($errors, -3);

            if (empty($errors)) {
                echo "<div class='success'>✓ No recent errors found</div>";
            } else {
                echo "<div class='warning'>Found " . count($errors) . " recent error(s):</div>";
                foreach (array_reverse($errors) as $i => $error) {
                    echo "<h3>Error " . ($i + 1) . ":</h3>";
                    echo "<pre>" . htmlspecialchars($error) . "</pre>";
                }
            }
        } else {
            echo "<div class='warning'>Log file not found</div>";
        }

        // Check 4: Composer Packages
        echo "<h2>4. Required Packages</h2>";
        $composerLock = $baseDir . '/composer.lock';
        if (file_exists($composerLock)) {
            $lock = json_decode(file_get_contents($composerLock), true);
            $installed = [];
            foreach ($lock['packages'] as $package) {
                $installed[$package['name']] = $package['version'];
            }

            $required = [
                'barryvdh/laravel-dompdf' => 'PDF export',
                'phpoffice/phpword' => 'Word export',
                'stichoza/google-translate-php' => 'Translation',
            ];

            echo "<table>";
            echo "<tr><th>Package</th><th>Purpose</th><th>Status</th></tr>";
            foreach ($required as $package => $purpose) {
                if (isset($installed[$package])) {
                    echo "<tr><td>{$package}</td><td>{$purpose}</td><td class='success'>✓ {$installed[$package]}</td></tr>";
                } else {
                    echo "<tr><td>{$package}</td><td>{$purpose}</td><td class='error'>✗ NOT INSTALLED</td></tr>";
                }
            }
            echo "</table>";
        }

        // Check 5: PHP Info
        echo "<h2>5. PHP Environment</h2>";
        echo "<table>";
        echo "<tr><th>Setting</th><th>Value</th></tr>";
        echo "<tr><td>PHP Version</td><td>" . PHP_VERSION . "</td></tr>";
        echo "<tr><td>Laravel Version</td><td>" . (file_exists($baseDir . '/vendor/laravel/framework/src/Illuminate/Foundation/Application.php') ? 'Installed' : 'Not found') . "</td></tr>";
        echo "<tr><td>Memory Limit</td><td>" . ini_get('memory_limit') . "</td></tr>";
        echo "<tr><td>Max Execution Time</td><td>" . ini_get('max_execution_time') . "s</td></tr>";
        echo "</table>";

        ?>

        <h2>6. Actions Needed</h2>
        <div class="info">
            <p><strong>إذا وجدت أخطاء أعلاه، نفذ الأوامر التالية:</strong></p>
            <pre>cd <?php echo $baseDir; ?>

git pull origin main
composer install
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear</pre>
        </div>

        <div class="warning">
            <p><strong>⚠️ تحذير أمني:</strong> احذف هذا الملف بعد الانتهاء من التشخيص!</p>
            <pre>rm <?php echo __FILE__; ?></pre>
        </div>
    </div>
</body>
</html>
