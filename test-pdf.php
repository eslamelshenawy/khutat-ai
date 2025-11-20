<?php
require __DIR__ . '/vendor/autoload.php';

use Mpdf\Mpdf;

try {
    echo "Testing mPDF with Arabic...\n";

    $mpdf = new Mpdf([
        'mode' => 'utf-8',
        'format' => 'A4',
        'orientation' => 'P',
        'directionality' => 'rtl',
        'autoScriptToLang' => true,
        'autoLangToFont' => true,
    ]);

    $html = '<!DOCTYPE html>
    <html dir="rtl" lang="ar">
    <head>
        <meta charset="UTF-8">
        <style>
            body { font-family: "DejaVu Sans", sans-serif; direction: rtl; text-align: right; }
            h1 { color: #1F4788; }
        </style>
    </head>
    <body>
        <h1>اختبار PDF العربي</h1>
        <p>هذا نص تجريبي باللغة العربية لاختبار عمل mPDF.</p>
        <p>الحروف يجب أن تظهر متصلة وبشكل صحيح.</p>
    </body>
    </html>';

    $mpdf->WriteHTML($html);

    $filename = __DIR__ . '/storage/app/test-arabic.pdf';
    $mpdf->Output($filename, \Mpdf\Output\Destination::FILE);

    echo "✅ PDF created successfully at: $filename\n";
    echo "File size: " . filesize($filename) . " bytes\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
