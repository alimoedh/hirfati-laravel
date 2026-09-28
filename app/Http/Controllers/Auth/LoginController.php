<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\{User, LoginLog};
use App\Services\{ActivityService, NotificationService};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, RateLimiter, Hash};
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect($this->dashboardUrl(Auth::user()->role));
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login'    => 'required|string',
            'password' => 'required|string',
        ]);

        $ip = $request->ip();
        $key = 'login:' . $ip;

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            throw ValidationException::withMessages([
                'login' => "تم تجاوز عدد محاولات الدخول. حاول بعد " . ceil($seconds / 60) . " دقيقة.",
            ]);
        }

        $login    = $request->input('login');
        $password = $request->input('password');
        $remember = $request->boolean('remember');

        $user = User::where('email', $login)->orWhere('phone', $login)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            RateLimiter::hit($key, 900);
            throw ValidationException::withMessages([
                'login' => 'البريد الإلكتروني/الهاتف أو كلمة المرور غير صحيحة',
            ]);
        }

        if (!$user->is_active) {
            throw ValidationException::withMessages([
                'login' => 'حسابك غير مفعل. تواصل مع الإدارة.',
            ]);
        }

        Auth::login($user, $remember);
        RateLimiter::clear($key);

        LoginLog::create([
            'user_id'    => $user->id,
            'ip_address' => $ip,
            'user_agent' => $request->userAgent(),
        ]);

        NotificationService::send($user->id, '👋 مرحباً بعودتك', 'تم تسجيل الدخول إلى حسابك', null, 'info');

        ActivityService::log($user->id, 'login', 'تسجيل دخول ناجح');

        return redirect()->intended($this->dashboardUrl($user->role));
    }

    public function logout(Request $request)
    {
        ActivityService::log(auth()->id(), 'logout', 'تسجيل خروج');
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
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
