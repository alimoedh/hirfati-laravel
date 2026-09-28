<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class VerifyController extends Controller
{
    public function show()
    {
        if (!auth()->check() || !auth()->user()->isCraftsman()) {
            return redirect()->route('auth.login');
        }
        return view('auth.verify', ['user' => auth()->user()]);
    }

    public function send(Request $request)
    {
        $request->validate([
            'verification_type' => 'required|in:phone,email',
            'phone'             => 'required_if:verification_type,phone|string|max:20',
        ]);

        $code = rand(100000, 999999);

        Cache::put('verify_code_' . auth()->id(), [
            'code'  => $code,
            'type'  => $request->verification_type,
            'phone' => $request->phone,
        ], now()->addMinutes(10));

        return back()->with('success', 'تم إرسال رمز التحقق إلى رقم هاتفك (للاختبار: ' . $code . ')');
    }

    public function verify(Request $request)
    {
        $request->validate(['code' => 'required|string']);

        $cached = Cache::get('verify_code_' . auth()->id());

        if (!$cached || $cached['code'] != $request->code) {
            return back()->with('error', 'الرمز غير صحيح');
        }

        auth()->user()->update(['is_verified' => true]);
        Cache::forget('verify_code_' . auth()->id());

        ActivityService::log(auth()->id(), 'verify', 'تم التحقق من الهوية');

        return back()->with('success', '✅ تم التحقق من هويتك بنجاح!');
    }
}
