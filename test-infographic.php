<?php
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Test Arabic text fixing
class TestInfographic {
    protected function fixArabicText($text): string
    {
        if (empty($text)) {
            return $text;
        }

        // For Arabic text, we need to reverse it for GD
        $text = mb_convert_encoding($text, 'HTML-ENTITIES', 'UTF-8');
        $text = preg_replace('/&([a-z]{1,2})(acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml|caron);/i', '$1', $text);
        $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');

        // Simple reverse for RTL text
        if (preg_match('/\p{Arabic}/u', $text)) {
            // Split by words/spaces to keep numbers in correct order
            $parts = preg_split('/([\s\d]+)/u', $text, -1, PREG_SPLIT_DELIM_CAPTURE);
            $reversed = array_reverse($parts);
            $text = implode('', $reversed);
        }

        return $text;
    }

    public function test() {
        $testTexts = [
            'خطة العمل',
            'نسبة الإكمال',
            'عدد الفصول',
            'الرؤية والمهمة',
            'test 123',
        ];

        foreach ($testTexts as $text) {
            $fixed = $this->fixArabicText($text);
            echo "Original: $text\n";
            echo "Fixed:    $fixed\n";
            echo "---\n";
        }
    }
}

$tester = new TestInfographic();
$tester->test();
