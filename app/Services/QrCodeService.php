<?php

namespace App\Services;

use App\Models\BusinessPlan;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrCodeService
{
    /**
     * Generate QR code for business plan share link
     */
    public function generateForBusinessPlan(BusinessPlan $plan, string $shareToken = null): string
    {
        $url = $shareToken
            ? route('shared-plan.view', ['token' => $shareToken])
            : route('business-plans.show', $plan);

        return QrCode::size(300)
            ->margin(2)
            ->format('png')
            ->generate($url);
    }

    /**
     * Generate QR code with logo/branding
     */
    public function generateWithLogo(string $url, string $logoPath = null): string
    {
        $qr = QrCode::size(400)
            ->margin(2)
            ->format('png')
            ->errorCorrection('H'); // High error correction for logo

        if ($logoPath && file_exists($logoPath)) {
            $qr->merge($logoPath, 0.3, true);
        }

        return $qr->generate($url);
    }

    /**
     * Generate QR code and save to file
     */
    public function generateAndSave(BusinessPlan $plan, string $shareToken = null): string
    {
        $url = $shareToken
            ? route('shared-plan.view', ['token' => $shareToken])
            : route('business-plans.show', $plan);

        $filename = 'qr_plan_' . $plan->id . '_' . time() . '.png';
        $path = storage_path('app/public/qr-codes/' . $filename);

        // Ensure directory exists
        if (!file_exists(storage_path('app/public/qr-codes'))) {
            mkdir(storage_path('app/public/qr-codes'), 0755, true);
        }

        QrCode::size(400)
            ->margin(2)
            ->format('png')
            ->generate($url, $path);

        return 'qr-codes/' . $filename;
    }

    /**
     * Generate downloadable QR code
     */
    public function generateDownloadResponse(BusinessPlan $plan, string $shareToken = null)
    {
        $url = $shareToken
            ? route('shared-plan.view', ['token' => $shareToken])
            : route('business-plans.show', $plan);

        $qrCode = QrCode::size(500)
            ->margin(2)
            ->format('png')
            ->generate($url);

        return response($qrCode, 200, [
            'Content-Type' => 'image/png',
            'Content-Disposition' => 'attachment; filename="business-plan-qr-' . $plan->id . '.png"'
        ]);
    }

    /**
     * Generate QR code for vCard (contact information)
     */
    public function generateVCard(BusinessPlan $plan): string
    {
        $vcard = "BEGIN:VCARD\n";
        $vcard .= "VERSION:3.0\n";
        $vcard .= "FN:" . $plan->company_name . "\n";
        $vcard .= "ORG:" . $plan->company_name . "\n";
        $vcard .= "TITLE:Business Plan\n";
        $vcard .= "URL:" . route('business-plans.show', $plan) . "\n";
        $vcard .= "NOTE:" . $plan->title . "\n";
        $vcard .= "END:VCARD";

        return QrCode::size(300)
            ->margin(2)
            ->format('png')
            ->generate($vcard);
    }
}
