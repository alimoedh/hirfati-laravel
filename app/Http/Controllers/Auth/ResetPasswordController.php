<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Hash};
use Illuminate\Support\Carbon;

class ResetPasswordController extends Controller
{
    public function showResetForm(Request $request, string $token)
    {
        $reset = DB::table('password_reset_tokens')->where('token', $token)->first();

        if (!$reset) {
            return view('auth.reset-password', ['token' => null, 'error' => 'الرابط غير صالح']);
        }

        if (Carbon::parse($reset->created_at)->addHour()->isPast()) {
            return view('auth.reset-password', ['token' => null, 'error' => 'الرابط منتهي الصلاحية']);
        }

        return view('auth.reset-password', ['token' => $token, 'email' => $reset->email]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token'            => 'required',
            'new_password'     => 'required|string|min:6',
            'confirm_password' => 'required|same:new_password',
        ]);

        $reset = DB::table('password_reset_tokens')->where('token', $request->token)->first();

        if (!$reset) {
            return back()->with('error', 'الرابط غير صالح أو منتهي الصلاحية');
        }

        if (Carbon::parse($reset->created_at)->addHour()->isPast()) {
            return back()->with('error', 'الرابط منتهي الصلاحية');
        }

        $user = User::where('email', $reset->email)->first();
        if (!$user) return back()->with('error', 'المستخدم غير موجود');

        $user->update(['password' => Hash::make($request->new_password)]);

        DB::table('password_reset_tokens')->where('email', $reset->email)->delete();

        ActivityService::log($user->id, 'reset_password', 'إعادة تعيين كلمة المرور');

        return redirect('/auth/login')->with('success', 'تم تغيير كلمة المرور بنجاح. يمكنك الآن تسجيل الدخول.');
    }
}
