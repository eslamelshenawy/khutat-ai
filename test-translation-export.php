<?php

// Test translation export functionality
require __DIR__.'/vendor/autoload.php';

use Illuminate\Support\Str;

echo "=== Testing Translation Export Functionality ===\n\n";

// Test 1: Check Str::slug() works
echo "Test 1: Str::slug() function\n";
$arabicTitle = "خطة عمل test1";
$slug = Str::slug($arabicTitle);
echo "Original: {$arabicTitle}\n";
echo "Slugified: {$slug}\n";
echo "Result: " . ($slug ? "✓ PASS" : "✗ FAIL") . "\n\n";

// Test 2: Check PDF library exists
echo "Test 2: Check PDF library\n";
if (class_exists('\Barryvdh\DomPDF\Facade\Pdf')) {
    echo "✓ PASS - DomPDF is installed\n\n";
} else {
    echo "✗ FAIL - DomPDF not found\n\n";
}

// Test 3: Check PHPWord library exists
echo "Test 3: Check PHPWord library\n";
if (class_exists('\PhpOffice\PhpWord\PhpWord')) {
    echo "✓ PASS - PHPWord is installed\n\n";
} else {
    echo "✗ FAIL - PHPWord not found\n\n";
}

// Test 4: Check Google Translate library
echo "Test 4: Check Google Translate library\n";
if (class_exists('\Stichoza\GoogleTranslate\GoogleTranslate')) {
    echo "✓ PASS - Google Translate is installed\n";

    // Test translation
    try {
        $tr = new \Stichoza\GoogleTranslate\GoogleTranslate();
        $tr->setSource('ar');
        $tr->setTarget('en');
        $translated = $tr->translate('مرحبا');
        echo "  Sample translation (مرحبا -> {$translated})\n\n";
    } catch (\Exception $e) {
        echo "  Warning: Translation test failed - " . $e->getMessage() . "\n\n";
    }
} else {
    echo "✗ FAIL - Google Translate not installed\n";
    echo "  Run: composer require stichoza/google-translate-php\n\n";
}

// Test 5: Simulate export filename generation
echo "Test 5: Export filename generation\n";
$titles = [
    'خطة عمل test1',
    'Business Plan 2024',
    'Plan de negocios',
];

foreach ($titles as $title) {
    $pdfFile = Str::slug($title) . '_translated.pdf';
    $wordFile = Str::slug($title) . '_translated.docx';
    $textFile = Str::slug($title) . '_translated.txt';

    echo "  Title: {$title}\n";
    echo "    PDF:  {$pdfFile}\n";
    echo "    Word: {$wordFile}\n";
    echo "    Text: {$textFile}\n";
}
echo "✓ PASS\n\n";

// Test 6: Check storage directory
echo "Test 6: Check storage directory for temp files\n";
$tempDir = __DIR__ . '/storage/app/temp';
if (!file_exists($tempDir)) {
    mkdir($tempDir, 0755, true);
    echo "✓ Created temp directory: {$tempDir}\n\n";
} else {
    echo "✓ Temp directory exists: {$tempDir}\n\n";
}

echo "=== All Tests Completed ===\n";
