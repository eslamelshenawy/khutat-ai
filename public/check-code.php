<?php
/**
 * Quick code version checker
 * Access: https://start.al-investor.com/check-code.php
 * DELETE THIS FILE AFTER USE!
 */

header('Content-Type: text/plain; charset=utf-8');

echo "=== Code Version Checker ===\n\n";

$projectRoot = dirname(__DIR__);

// Check 1: Git commit
echo "1. Git Status:\n";
if (is_dir($projectRoot . '/.git')) {
    $commit = trim(shell_exec("cd $projectRoot && git log -1 --format='%h - %s (%cr)' 2>&1"));
    echo "   Last commit: $commit\n";

    $branch = trim(shell_exec("cd $projectRoot && git rev-parse --abbrev-ref HEAD 2>&1"));
    echo "   Branch: $branch\n\n";
} else {
    echo "   Not a git repo\n\n";
}

// Check 2: BusinessPlanTranslationController
echo "2. BusinessPlanTranslationController.php:\n";
$file = $projectRoot . '/app/Http/Controllers/BusinessPlanTranslationController.php';

if (file_exists($file)) {
    $content = file_get_contents($file);

    // Check imports
    if (strpos($content, 'use Illuminate\Support\Str;') !== false) {
        echo "   ✓ Has 'use Illuminate\\Support\\Str;'\n";
    } else {
        echo "   ✗ MISSING 'use Illuminate\\Support\\Str;' - NEEDS GIT PULL!\n";
    }

    // Check old slug()
    $oldCount = substr_count($content, 'slug($title)');
    if ($oldCount > 0) {
        echo "   ✗ Found $oldCount instances of old 'slug(\$title)' - NEEDS GIT PULL!\n";
    } else {
        echo "   ✓ No old 'slug(\$title)' found\n";
    }

    // Check new Str::slug()
    $newCount = substr_count($content, 'Str::slug($title)');
    if ($newCount > 0) {
        echo "   ✓ Found $newCount instances of 'Str::slug(\$title)'\n";
    } else {
        echo "   ✗ No 'Str::slug(\$title)' found - NEEDS GIT PULL!\n";
    }

    $modified = date('Y-m-d H:i:s', filemtime($file));
    echo "   File modified: $modified\n\n";
} else {
    echo "   ✗ File not found!\n\n";
}

// Check 3: Required packages
echo "3. Composer Packages:\n";
$composerLock = $projectRoot . '/composer.lock';
if (file_exists($composerLock)) {
    $lock = json_decode(file_get_contents($composerLock), true);
    $packages = [];
    foreach ($lock['packages'] as $pkg) {
        $packages[$pkg['name']] = $pkg['version'];
    }

    $check = [
        'barryvdh/laravel-dompdf',
        'phpoffice/phpword',
        'stichoza/google-translate-php',
    ];

    foreach ($check as $name) {
        if (isset($packages[$name])) {
            echo "   ✓ $name: {$packages[$name]}\n";
        } else {
            echo "   ✗ $name: NOT INSTALLED\n";
        }
    }
} else {
    echo "   composer.lock not found\n";
}

echo "\n=== END ===\n";
echo "\nIf you see errors, run:\n";
echo "  cd $projectRoot\n";
echo "  git pull origin main\n";
echo "  composer install\n";
echo "  php artisan config:clear\n";
echo "  php artisan cache:clear\n";
echo "\nDELETE THIS FILE: rm " . __FILE__ . "\n";
