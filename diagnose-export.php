<?php

/**
 * Diagnostic script to test export functionality
 * Upload this to server and run: php diagnose-export.php
 */

echo "=== Translation Export Diagnostic ===\n\n";

// Check 1: PHP Version
echo "1. PHP Version: " . PHP_VERSION . "\n";
if (version_compare(PHP_VERSION, '8.2.0', '>=')) {
    echo "   ✓ PHP version compatible\n\n";
} else {
    echo "   ✗ PHP version too old (need 8.2+)\n\n";
}

// Check 2: Check if file has Str import
echo "2. Checking BusinessPlanTranslationController.php...\n";
$controllerPath = __DIR__ . '/app/Http/Controllers/BusinessPlanTranslationController.php';
if (file_exists($controllerPath)) {
    $content = file_get_contents($controllerPath);

    // Check for Str import
    if (strpos($content, 'use Illuminate\Support\Str;') !== false) {
        echo "   ✓ Str import found\n";
    } else {
        echo "   ✗ Str import MISSING - need to git pull!\n";
    }

    // Check for slug() usage (should be Str::slug())
    if (strpos($content, 'slug($title)') !== false) {
        echo "   ✗ Old slug() usage found - need to git pull!\n";
    } else {
        echo "   ✓ No old slug() usage found\n";
    }

    // Check for Str::slug() usage (correct)
    if (strpos($content, 'Str::slug($title)') !== false) {
        echo "   ✓ Str::slug() usage found\n";
    } else {
        echo "   ✗ Str::slug() usage NOT found\n";
    }
    echo "\n";
} else {
    echo "   ✗ Controller file not found!\n\n";
}

// Check 3: Test Str::slug() if Laravel loaded
echo "3. Testing Str::slug() function...\n";
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require __DIR__ . '/vendor/autoload.php';

    try {
        $testTitle = "خطة عمل test1";
        $slug = \Illuminate\Support\Str::slug($testTitle);
        echo "   ✓ Str::slug() works\n";
        echo "   Input:  {$testTitle}\n";
        echo "   Output: {$slug}\n";
        echo "   File:   {$slug}_translated.pdf\n\n";
    } catch (\Exception $e) {
        echo "   ✗ Str::slug() failed: " . $e->getMessage() . "\n\n";
    }
} else {
    echo "   ⚠ Vendor autoload not found, skipping\n\n";
}

// Check 4: Required libraries
echo "4. Checking required libraries...\n";
$composerJson = __DIR__ . '/composer.json';
if (file_exists($composerJson)) {
    $composer = json_decode(file_get_contents($composerJson), true);
    $required = [
        'barryvdh/laravel-dompdf' => 'PDF export',
        'phpoffice/phpword' => 'Word export',
        'stichoza/google-translate-php' => 'Translation',
    ];

    foreach ($required as $package => $purpose) {
        if (isset($composer['require'][$package])) {
            echo "   ✓ {$package} ({$purpose})\n";
        } else {
            echo "   ✗ {$package} ({$purpose}) - MISSING\n";
        }
    }
    echo "\n";
}

// Check 5: Storage directory
echo "5. Checking storage directory...\n";
$tempDir = __DIR__ . '/storage/app/temp';
if (file_exists($tempDir)) {
    echo "   ✓ Temp directory exists\n";
    if (is_writable($tempDir)) {
        echo "   ✓ Temp directory is writable\n";
    } else {
        echo "   ✗ Temp directory is NOT writable\n";
        echo "   Run: chmod 755 {$tempDir}\n";
    }
} else {
    echo "   ⚠ Temp directory doesn't exist, will be created\n";
}
echo "\n";

// Check 6: Git status
echo "6. Checking git status...\n";
if (is_dir(__DIR__ . '/.git')) {
    $lastCommit = trim(shell_exec('cd ' . __DIR__ . ' && git log -1 --oneline 2>&1'));
    echo "   Last commit: {$lastCommit}\n";

    $status = shell_exec('cd ' . __DIR__ . ' && git status 2>&1');
    if (strpos($status, 'behind') !== false) {
        echo "   ✗ Code is behind remote - need git pull!\n";
    } else {
        echo "   ✓ Code is up to date\n";
    }
} else {
    echo "   ⚠ Not a git repository\n";
}
echo "\n";

echo "=== Diagnostic Complete ===\n\n";

echo "If you see errors above, run these commands:\n";
echo "1. git pull origin main\n";
echo "2. composer install\n";
echo "3. php artisan config:clear\n";
echo "4. php artisan cache:clear\n";
echo "5. php artisan view:clear\n";
