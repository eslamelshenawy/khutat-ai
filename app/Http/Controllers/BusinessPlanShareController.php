<?php

namespace App\Http\Controllers;

use App\Models\BusinessPlan;
use App\Models\BusinessPlanShare;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class BusinessPlanShareController extends Controller
{
    public function create(BusinessPlan $businessPlan)
    {
        Gate::authorize('update', $businessPlan);

        return view('business-plans.share.create', compact('businessPlan'));
    }

    public function store(Request $request, BusinessPlan $businessPlan)
    {
        Gate::authorize('update', $businessPlan);

        $validated = $request->validate([
            'type' => 'required|in:public,private',
            'password' => 'required_if:type,private|nullable|min:6',
            'permission' => 'required|in:view,comment,edit',
            'expires_in_days' => 'nullable|integer|min:1|max:365',
        ]);

        $share = $businessPlan->shares()->create([
            'shared_by' => auth()->id(),
            'type' => $validated['type'],
            'password' => $validated['type'] === 'private' && $validated['password']
                ? Hash::make($validated['password'])
                : null,
            'permission' => $validated['permission'],
            'expires_at' => $validated['expires_in_days'] ?? null
                ? now()->addDays($validated['expires_in_days'])
                : null,
            'is_active' => true,
        ]);

        Log::info('Business plan shared', [
            'plan_id' => $businessPlan->id,
            'share_id' => $share->id,
            'type' => $share->type,
        ]);

        return redirect()->back()->with('success', 'تم إنشاء رابط المشاركة بنجاح!')->with('share_link', $share->getShareUrl());
    }

    public function view(Request $request, $token)
    {
        $share = BusinessPlanShare::where('token', $token)->firstOrFail();

        if (!$share->isActive()) {
            abort(403, 'هذا الرابط غير نشط أو منتهي الصلاحية');
        }

        if ($share->type === 'private') {
            if (!$request->session()->has('share_authenticated_' . $share->id)) {
                return view('business-plans.share.password', compact('share'));
            }
        }

        $share->incrementViewCount([
            'country' => $request->header('CF-IPCountry'),
            'device' => $this->detectDevice($request),
        ]);

        $businessPlan = $share->businessPlan()->with(['chapters'])->first();

        return view('business-plans.share.view', compact('businessPlan', 'share'));
    }

    public function authenticate(Request $request, $token)
    {
        $share = BusinessPlanShare::where('token', $token)->firstOrFail();

        if (!$share->isActive()) {
            abort(403, 'هذا الرابط غير نشط أو منتهي الصلاحية');
        }

        $request->validate([
            'password' => 'required',
        ]);

        if (Hash::check($request->password, $share->password)) {
            $request->session()->put('share_authenticated_' . $share->id, true);
            return redirect()->route('shared-plan.view', ['token' => $token]);
        }

        return back()->withErrors(['password' => 'كلمة المرور غير صحيحة']);
    }

    public function deactivate(BusinessPlanShare $share)
    {
        Gate::authorize('update', $share->businessPlan);

        $share->update(['is_active' => false]);

        Log::info('Share link deactivated', [
            'share_id' => $share->id,
            'plan_id' => $share->business_plan_id,
        ]);

        return redirect()->back()->with('success', 'تم تعطيل رابط المشاركة بنجاح');
    }

    public function analytics(BusinessPlanShare $share)
    {
        Gate::authorize('view', $share->businessPlan);

        $views = $share->views()
            ->selectRaw('DATE(viewed_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->take(30)
            ->get();

        $topReferrers = $share->views()
            ->selectRaw('referer, COUNT(*) as count')
            ->whereNotNull('referer')
            ->groupBy('referer')
            ->orderBy('count', 'desc')
            ->take(10)
            ->get();

        return view('business-plans.share.analytics', compact('share', 'views', 'topReferrers'));
    }

    public function sendEmail(Request $request, BusinessPlanShare $share)
    {
        Gate::authorize('update', $share->businessPlan);

        $validated = $request->validate([
            'emails_text' => 'required|string',
            'message' => 'nullable|string|max:500',
        ]);

        // Convert comma-separated emails to array and validate each
        $emailsArray = array_map('trim', explode(',', $validated['emails_text']));
        $emailsArray = array_filter($emailsArray);

        // Validate each email
        foreach ($emailsArray as $email) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return redirect()->back()->withErrors(['emails_text' => "البريد الإلكتروني غير صالح: {$email}"]);
            }
        }

        foreach ($emailsArray as $email) {
            try {
                Mail::send('emails.shared-plan', [
                    'plan' => $share->businessPlan,
                    'share' => $share,
                    'message' => $validated['message'] ?? '',
                    'sender' => auth()->user(),
                ], function ($mail) use ($email, $share) {
                    $mail->to($email)
                        ->subject('تمت مشاركة خطة عمل معك: ' . $share->businessPlan->title);
                });

                Log::info('Share email sent', [
                    'share_id' => $share->id,
                    'recipient' => $email,
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to send share email', [
                    'share_id' => $share->id,
                    'recipient' => $email,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return redirect()->back()->with('success', 'تم إرسال دعوات المشاركة بنجاح');
    }

    protected function detectDevice(Request $request): string
    {
        $userAgent = $request->userAgent();

        if (preg_match('/mobile/i', $userAgent)) {
            return 'mobile';
        }

        if (preg_match('/tablet/i', $userAgent)) {
            return 'tablet';
        }

        return 'desktop';
    }
}
