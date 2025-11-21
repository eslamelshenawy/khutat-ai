<?php

// Simple test for Str::slug without Laravel bootstrap
echo "=== Simple Slug Test ===\n\n";

// Manual slug implementation (similar to Laravel's Str::slug)
function simple_slug($title, $separator = '-', $language = 'en') {
    $title = mb_strtolower($title);

    // Replace non-letter or digits by separator
    $title = preg_replace('![^' . preg_quote($separator) . '\pL\pN\s]+!u', '', $title);

    // Replace all separator-like characters by separator
    $title = preg_replace('![' . preg_quote($separator) . '\s]+!u', $separator, $title);

    return trim($title, $separator);
}

// Test cases
$testCases = [
    'خطة عمل test1',
    'Business Plan 2024',
    'Plan d\'affaires',
    'Test@#$%Plan',
];

echo "Testing slug generation:\n\n";
foreach ($testCases as $title) {
    $slug = simple_slug($title);
    echo "Original: {$title}\n";
    echo "Slug:     {$slug}\n";
    echo "Filename: {$slug}_translated.pdf\n\n";
}

echo "✓ All slug tests completed\n";
