<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorController extends Controller
{
    public function show(Request $request)
    {
        if (!auth()->check()) return redirect()->route('auth.login');

        $google2fa = new Google2FA();
        $secret = session('2fa_secret') ?? $google2fa->generateSecretKey();
        session(['2fa_secret' => $secret]);

        $qrCodeUrl = $google2fa->getQRCodeUrl(
            config('hirfati.site_name'),
            auth()->user()->email,
            $secret
        );

        return view('auth.2fa', compact('qrCodeUrl', 'secret'));
    }

    public function verify(Request $request)
    {
        $request->validate(['code' => 'required|string']);

        $secret = session('2fa_secret');
        if (!$secret) return back()->with('error', 'انتهت الجلسة، أعد المحاولة');

        $google2fa = new Google2FA();

        if ($google2fa->verifyKey($secret, $request->code)) {
            session(['2fa_verified' => true]);
            ActivityService::log(auth()->id(), '2fa', 'تم التحقق بخطوتين');
            return redirect($this->dashboardUrl(auth()->user()->role));
        }

        return back()->with('error', 'الرمز غير صحيح');
    }

    private function dashboardUrl(string $role): string
    {
        return match($role) {
            'admin'     => route('admin.dashboard'),
            'craftsman' => route('craftsman.dashboard'),
            default     => route('client.dashboard'),
        };
    }
}
