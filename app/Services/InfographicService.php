<?php

namespace App\Services;

use App\Models\BusinessPlan;
use Illuminate\Support\Facades\Storage;

class InfographicService
{
    protected $width = 1200;
    protected $height = 1800;
    protected $image;

    /**
     * Generate infographic for business plan
     */
    public function generateInfographic(BusinessPlan $plan): string
    {
        $plan->load(['chapters', 'user']);

        // Create blank canvas
        $this->image = imagecreatetruecolor($this->width, $this->height);

        // Define colors
        $colors = [
            'bg_primary' => imagecolorallocate($this->image, 31, 71, 136),     // #1F4788
            'bg_dark' => imagecolorallocate($this->image, 13, 40, 71),         // #0D2847
            'bg_medium' => imagecolorallocate($this->image, 44, 95, 141),      // #2C5F8D
            'white' => imagecolorallocate($this->image, 255, 255, 255),
            'gold' => imagecolorallocate($this->image, 255, 215, 0),
            'green' => imagecolorallocate($this->image, 76, 175, 80),
            'blue' => imagecolorallocate($this->image, 33, 150, 243),
            'orange' => imagecolorallocate($this->image, 255, 152, 0),
            'gray' => imagecolorallocate($this->image, 170, 170, 170),
        ];

        // Fill background
        imagefilledrectangle($this->image, 0, 0, $this->width, $this->height, $colors['bg_primary']);

        // Add sections
        $this->addHeader($plan, $colors);
        $this->addStats($plan, $colors);
        $this->addVisionMission($plan, $colors);
        $this->addChaptersList($plan, $colors);
        $this->addFooter($plan, $colors);

        // Save to storage
        $filename = 'infographic_' . $plan->id . '_' . time() . '.png';
        $path = storage_path('app/public/infographics/' . $filename);

        // Ensure directory exists
        if (!file_exists(storage_path('app/public/infographics'))) {
            mkdir(storage_path('app/public/infographics'), 0755, true);
        }

        imagepng($this->image, $path, 9);
        imagedestroy($this->image);

        return 'infographics/' . $filename;
    }

    /**
     * Add header section
     */
    protected function addHeader(BusinessPlan $plan, array $colors)
    {
        // Header background
        imagefilledrectangle($this->image, 0, 0, $this->width, 280, $colors['bg_dark']);

        // Company name (centered, large)
        $this->addCenteredText($plan->company_name, 90, $colors['white'], 5);

        // Plan title (centered, medium)
        $this->addCenteredText($plan->title, 160, $colors['gold'], 4);

        // Status badge
        $statusText = $this->getStatusLabel($plan->status);
        $badgeX = ($this->width - 200) / 2;
        $badgeY = 220;

        imagefilledrectangle($this->image, $badgeX, $badgeY, $badgeX + 200, $badgeY + 45, $colors['green']);
        imagerectangle($this->image, $badgeX, $badgeY, $badgeX + 200, $badgeY + 45, $colors['white']);

        $this->addCenteredText($statusText, $badgeY + 28, $colors['white'], 3);
    }

    /**
     * Add statistics section
     */
    protected function addStats(BusinessPlan $plan, array $colors)
    {
        $y = 330;
        $boxWidth = 340;
        $boxHeight = 160;
        $spacing = 30;

        // Stats data
        $stats = [
            [
                'label' => 'نسبة الإكمال',
                'value' => $plan->completion_percentage . '%',
                'color' => $colors['green']
            ],
            [
                'label' => 'عدد الفصول',
                'value' => (string)$plan->chapters->count(),
                'color' => $colors['blue']
            ],
            [
                'label' => 'إجمالي الكلمات',
                'value' => number_format($plan->chapters->sum('word_count')),
                'color' => $colors['orange']
            ]
        ];

        $startX = ($this->width - ($boxWidth * 3 + $spacing * 2)) / 2;

        foreach ($stats as $i => $stat) {
            $x = $startX + ($boxWidth + $spacing) * $i;

            // Draw box
            imagefilledrectangle($this->image, $x, $y, $x + $boxWidth, $y + $boxHeight, $stat['color']);
            imagerectangle($this->image, $x, $y, $x + $boxWidth, $y + $boxHeight, $colors['white']);

            // Value (large, centered)
            $this->addTextInBox($stat['value'], $x, $y + 60, $boxWidth, $colors['white'], 5);

            // Label (small, centered)
            $this->addTextInBox($stat['label'], $x, $y + 120, $boxWidth, $colors['white'], 3);
        }
    }

    /**
     * Add vision and mission
     */
    protected function addVisionMission(BusinessPlan $plan, array $colors)
    {
        $y = 550;
        $padding = 50;
        $boxWidth = $this->width - ($padding * 2);

        if ($plan->vision) {
            imagefilledrectangle($this->image, $padding, $y, $padding + $boxWidth, $y + 120, $colors['bg_medium']);
            imagerectangle($this->image, $padding, $y, $padding + $boxWidth, $y + 120, $colors['white']);

            $this->addText('الرؤية', $padding + 20, $y + 35, $colors['gold'], 4);

            $visionText = $this->truncateText($plan->vision, 100);
            $this->addText($visionText, $padding + 20, $y + 75, $colors['white'], 3);

            $y += 150;
        }

        if ($plan->mission) {
            imagefilledrectangle($this->image, $padding, $y, $padding + $boxWidth, $y + 120, $colors['bg_medium']);
            imagerectangle($this->image, $padding, $y, $padding + $boxWidth, $y + 120, $colors['white']);

            $this->addText('الرسالة', $padding + 20, $y + 35, $colors['gold'], 4);

            $missionText = $this->truncateText($plan->mission, 100);
            $this->addText($missionText, $padding + 20, $y + 75, $colors['white'], 3);
        }
    }

    /**
     * Add chapters list
     */
    protected function addChaptersList(BusinessPlan $plan, array $colors)
    {
        $y = 1050;

        $this->addCenteredText('الفصول', $y, $colors['gold'], 4);

        $y += 60;
        $chapters = $plan->chapters->take(8);

        foreach ($chapters as $index => $chapter) {
            // Circle for number
            $circleX = 120;
            $circleY = $y + 15;
            $circleRadius = 22;

            imagefilledellipse($this->image, $circleX, $circleY, $circleRadius * 2, $circleRadius * 2, $colors['gold']);
            imageellipse($this->image, $circleX, $circleY, $circleRadius * 2, $circleRadius * 2, $colors['white']);

            // Number
            $this->addText((string)($index + 1), $circleX - 8, $circleY + 7, $colors['bg_dark'], 3);

            // Chapter title
            $chapterTitle = $this->truncateText($chapter->title, 70);
            $this->addText($chapterTitle, 170, $y + 20, $colors['white'], 3);

            $y += 55;
        }

        if ($plan->chapters->count() > 8) {
            $this->addCenteredText('+ ' . ($plan->chapters->count() - 8) . ' فصول أخرى', $y + 20, $colors['gray'], 3);
        }
    }

    /**
     * Add footer
     */
    protected function addFooter(BusinessPlan $plan, array $colors)
    {
        $y = $this->height - 110;

        imagefilledrectangle($this->image, 0, $y, $this->width, $this->height, $colors['bg_dark']);

        $this->addCenteredText('تم إنشاؤها بواسطة Business Plan Wizard', $y + 40, $colors['white'], 3);
        $this->addCenteredText(now()->format('Y-m-d'), $y + 75, $colors['gray'], 2);
    }

    /**
     * Add centered text
     */
    protected function addCenteredText($text, $y, $color, $size)
    {
        $fontSize = $this->getFontSize($size);
        $font = $this->getFontPath();

        // Fix Arabic text direction
        $text = $this->fixArabicText($text);

        if ($font && file_exists($font)) {
            $bbox = imagettfbbox($fontSize, 0, $font, $text);
            $textWidth = abs($bbox[4] - $bbox[0]);
            $x = ($this->width - $textWidth) / 2;
            imagettftext($this->image, $fontSize, 0, $x, $y, $color, $font, $text);
        } else {
            // Fallback to built-in font
            $x = ($this->width - (strlen($text) * imagefontwidth($size))) / 2;
            imagestring($this->image, $size, $x, $y - 10, $text, $color);
        }
    }

    /**
     * Add text at specific position
     */
    protected function addText($text, $x, $y, $color, $size)
    {
        $fontSize = $this->getFontSize($size);
        $font = $this->getFontPath();

        // Fix Arabic text direction
        $text = $this->fixArabicText($text);

        if ($font && file_exists($font)) {
            imagettftext($this->image, $fontSize, 0, $x, $y, $color, $font, $text);
        } else {
            imagestring($this->image, $size, $x, $y - 10, $text, $color);
        }
    }

    /**
     * Add text centered in box
     */
    protected function addTextInBox($text, $boxX, $y, $boxWidth, $color, $size)
    {
        $fontSize = $this->getFontSize($size);
        $font = $this->getFontPath();

        // Fix Arabic text direction
        $text = $this->fixArabicText($text);

        if ($font && file_exists($font)) {
            $bbox = imagettfbbox($fontSize, 0, $font, $text);
            $textWidth = abs($bbox[4] - $bbox[0]);
            $x = $boxX + ($boxWidth - $textWidth) / 2;
            imagettftext($this->image, $fontSize, 0, $x, $y, $color, $font, $text);
        } else {
            $x = $boxX + ($boxWidth - (strlen($text) * imagefontwidth($size))) / 2;
            imagestring($this->image, $size, $x, $y - 10, $text, $color);
        }
    }

    /**
     * Get font size based on level
     */
    protected function getFontSize($level): int
    {
        return match($level) {
            5 => 42,
            4 => 28,
            3 => 20,
            2 => 16,
            default => 14,
        };
    }

    /**
     * Get font path (try multiple locations)
     */
    protected function getFontPath(): ?string
    {
        // Only check paths within allowed directories
        // Try Arabic fonts first
        $paths = [
            storage_path('fonts/Tajawal-Bold.ttf'),
            storage_path('fonts/Tajawal-Regular.ttf'),
            public_path('fonts/Tajawal-Regular.ttf'),
            storage_path('fonts/Arial.ttf'),
            public_path('fonts/Arial.ttf'),
            base_path('fonts/Arial.ttf'),
        ];

        foreach ($paths as $path) {
            // Suppress errors from open_basedir restrictions
            if (@file_exists($path)) {
                return $path;
            }
        }

        // Return null to use built-in GD fonts (won't work for Arabic)
        return null;
    }

    /**
     * Fix Arabic text for GD (reshape and reverse for RTL)
     */
    protected function fixArabicText($text): string
    {
        if (empty($text)) {
            return $text;
        }

        // Check if text contains Arabic
        if (!preg_match('/\p{Arabic}/u', $text)) {
            return $text; // Not Arabic, return as-is
        }

        // Use ArPHP library for proper Arabic text shaping
        if (class_exists('\ArPHP\I18N\Arabic')) {
            try {
                $arabic = new \ArPHP\I18N\Arabic();

                // Glyphs method: converts Arabic characters to their correct form
                $text = $arabic->utf8Glyphs($text);

                return $text;
            } catch (\Exception $e) {
                // Fallback to simple reverse if library fails
                return $this->reverseArabicText($text);
            }
        }

        // Fallback: simple reverse
        return $this->reverseArabicText($text);
    }

    /**
     * Simple reverse for Arabic text (fallback)
     */
    protected function reverseArabicText($text): string
    {
        // Convert to array of characters
        $chars = preg_split('//u', $text, -1, PREG_SPLIT_NO_EMPTY);

        // Reverse the array
        $reversed = array_reverse($chars);

        // Join back
        return implode('', $reversed);
    }

    /**
     * Get status label in Arabic
     */
    protected function getStatusLabel($status): string
    {
        return match($status) {
            'draft' => 'مسودة',
            'in_progress' => 'قيد التنفيذ',
            'review' => 'مراجعة',
            'completed' => 'مكتمل',
            'archived' => 'مؤرشف',
            default => $status,
        };
    }

    /**
     * Truncate text to specified length
     */
    protected function truncateText($text, $length): string
    {
        if (mb_strlen($text) > $length) {
            return mb_substr($text, 0, $length) . '...';
        }
        return $text;
    }
}
