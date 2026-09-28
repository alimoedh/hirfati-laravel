<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->with('error', 'البريد الإلكتروني غير مسجل لدينا');
        }

        $token = Str::random(64);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            ['token' => $token, 'created_at' => now()]
        );

        $resetLink = url('/auth/reset-password/' . $token);

        return back()->with('success',
            'تم إرسال رابط إعادة تعيين كلمة المرور إلى بريدك الإلكتروني.<br>' .
            '<small>للاختبار: <a href="' . $resetLink . '">' . $resetLink . '</a></small>'
        );
    }
}
