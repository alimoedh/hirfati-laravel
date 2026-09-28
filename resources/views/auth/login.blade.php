@extends('layouts.auth')
@section('title', 'تسجيل الدخول')

@push('styles')
<style>
    /* ============================================
       Floating Tools Background
       ============================================ */
    .tool-icon {
        position: absolute;
        color: rgba(212, 162, 76, 0.15);
        animation: floatTool 8s ease-in-out infinite;
        pointer-events: none;
        user-select: none;
        filter: drop-shadow(0 0 20px rgba(212, 162, 76, 0.2));
    }

    .tool-icon.bright {
        color: rgba(212, 162, 76, 0.25);
        filter: drop-shadow(0 0 30px rgba(212, 162, 76, 0.35));
    }

    .tool-icon.blurred {
        filter: blur(2px);
        opacity: 0.6;
    }

    @keyframes floatTool {
        0%, 100% {
            transform: translateY(0px) rotate(var(--rot, 0deg));
        }
        50% {
            transform: translateY(-40px) rotate(calc(var(--rot, 0deg) + 15deg));
        }
    }

    @keyframes rotateSlow {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .tool-rotate {
        animation: rotateSlow 40s linear infinite;
        color: rgba(212, 162, 76, 0.08);
    }

    /* ============================================
       Glass Card
       ============================================ */
    .glass-card-login {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.03) 100%);
        backdrop-filter: blur(40px) saturate(180%);
        -webkit-backdrop-filter: blur(40px) saturate(180%);
        border: 1.5px solid rgba(255, 255, 255, 0.15);
        box-shadow:
            0 20px 60px rgba(0, 0, 0, 0.5),
            inset 0 1px 0 rgba(255, 255, 255, 0.15);
    }

    .glass-input-modern {
        background: rgba(255, 255, 255, 0.05);
        border: 1.5px solid rgba(255, 255, 255, 0.12);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .glass-input-modern:focus-within {
        background: rgba(255, 255, 255, 0.08);
        border-color: rgba(212, 162, 76, 0.6);
        box-shadow: 0 0 0 4px rgba(212, 162, 76, 0.15), 0 0 30px rgba(212, 162, 76, 0.2);
    }

    /* ============================================
       Gradient Button
       ============================================ */
    .btn-login-gradient {
        background: linear-gradient(135deg, #D4A24C 0%, #B8873A 100%);
        position: relative;
        overflow: hidden;
        transition: all 0.3s;
        box-shadow: 0 8px 24px rgba(212, 162, 76, 0.35);
    }

    .btn-login-gradient:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 40px rgba(212, 162, 76, 0.5);
    }

    .btn-login-gradient::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        transition: left 0.6s;
    }

    .btn-login-gradient:hover::before {
        left: 100%;
    }

    /* ============================================
       Vignette Effect
       ============================================ */
    .vignette {
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at center, transparent 0%, rgba(10, 20, 40, 0.6) 100%);
        pointer-events: none;
    }

    /* Small screen: hide some tools */
    @media (max-width: 768px) {
        .hide-mobile { display: none !important; }
    }
</style>
@endpush

@section('content')

<div class="min-h-screen relative overflow-hidden flex items-center justify-center p-4">

    {{-- ============================================
         Animated Tools Background
         ============================================ --}}

    {{-- 🔨 Hammers --}}
    <div class="tool-icon bright hide-mobile" style="top: 8%; left: 5%; font-size: 5rem; --rot: -20deg; animation-delay: 0s;">
        <i class="fa-solid fa-hammer"></i>
    </div>
    <div class="tool-icon blurred hide-mobile" style="bottom: 12%; right: 8%; font-size: 7rem; --rot: 25deg; animation-delay: 3s;">
        <i class="fa-solid fa-hammer"></i>
    </div>

    {{-- 🔧 Wrenches --}}
    <div class="tool-icon bright" style="top: 20%; right: 6%; font-size: 4rem; --rot: 15deg; animation-delay: 1s;">
        <i class="fa-solid fa-wrench"></i>
    </div>
    <div class="tool-icon blurred hide-mobile" style="bottom: 25%; left: 4%; font-size: 6rem; --rot: -30deg; animation-delay: 4s;">
        <i class="fa-solid fa-wrench"></i>
    </div>

    {{-- 🪛 Screwdriver --}}
    <div class="tool-icon bright hide-mobile" style="top: 55%; left: 8%; font-size: 4.5rem; --rot: 40deg; animation-delay: 2s;">
        <i class="fa-solid fa-screwdriver"></i>
    </div>

    {{-- 🎨 Paint Roller --}}
    <div class="tool-icon hide-mobile" style="top: 65%; right: 5%; font-size: 5rem; --rot: -15deg; animation-delay: 1.5s;">
        <i class="fa-solid fa-paint-roller"></i>
    </div>

    {{-- 📏 Ruler --}}
    <div class="tool-icon bright blurred hide-mobile" style="top: 40%; left: 2%; font-size: 6rem; --rot: 60deg; animation-delay: 5s;">
        <i class="fa-solid fa-ruler-combined"></i>
    </div>

    {{-- 🧰 Toolbox --}}
    <div class="tool-icon bright hide-mobile" style="bottom: 5%; left: 30%; font-size: 4.5rem; --rot: -10deg; animation-delay: 2.5s;">
        <i class="fa-solid fa-toolbox"></i>
    </div>

    {{-- ⚙️ Gears (rotating) --}}
    <div class="tool-rotate hide-mobile" style="top: 12%; right: 25%; font-size: 8rem;">
        <i class="fa-solid fa-gear"></i>
    </div>
    <div class="tool-rotate hide-mobile" style="bottom: 15%; left: 22%; font-size: 6rem; animation-direction: reverse;">
        <i class="fa-solid fa-gear"></i>
    </div>

    {{-- 🔩 Bolts / Nuts --}}
    <div class="tool-icon hide-mobile" style="top: 75%; right: 28%; font-size: 3rem; --rot: 0deg; animation-delay: 3.5s;">
        <i class="fa-solid fa-screwdriver-wrench"></i>
    </div>

    {{-- 🎨 Paint Brush --}}
    <div class="tool-icon bright blurred hide-mobile" style="top: 35%; right: 15%; font-size: 5rem; --rot: -25deg; animation-delay: 4.5s;">
        <i class="fa-solid fa-brush"></i>
    </div>

    {{-- 🔨 Extra hammer bottom right --}}
    <div class="tool-icon hide-mobile" style="bottom: 40%; right: 3%; font-size: 4rem; --rot: 20deg; animation-delay: 0.5s;">
        <i class="fa-solid fa-hammer"></i>
    </div>

    {{-- 🪚 Saw --}}
    <div class="tool-icon bright hide-mobile" style="top: 48%; right: 30%; font-size: 4rem; --rot: 10deg; animation-delay: 1.8s;">
        <i class="fa-solid fa-screwdriver-wrench"></i>
    </div>

    {{-- Small accent dots --}}
    <div class="tool-icon hide-mobile" style="top: 30%; left: 45%; font-size: 1.5rem; --rot: 0deg; animation-delay: 1s;">
        <i class="fa-solid fa-circle"></i>
    </div>
    <div class="tool-icon hide-mobile" style="bottom: 20%; right: 45%; font-size: 1.5rem; --rot: 0deg; animation-delay: 3s;">
        <i class="fa-solid fa-circle"></i>
    </div>

    {{-- Vignette overlay --}}
    <div class="vignette"></div>

    {{-- ============================================
         Login Card - Centered
         ============================================ --}}
    <div class="relative z-10 w-full max-w-md animate-fade-up">

        {{-- Logo --}}
        <div class="text-center mb-6">
            <img src="{{ asset('assets/images/logo.png') }}" alt="{{ config('hirfati.site_name') }}"
                 class="w-40 h-auto mx-auto mb-4">
            <h1 class="text-3xl font-black text-white">مرحباً بعودتك</h1>
            <p class="text-white/50 text-sm mt-2">سجل دخولك للمتابعة</p>
        </div>

        {{-- Glass Card --}}
        <div class="glass-card-login rounded-3xl p-8">

            {{-- Errors --}}
            @if($errors->any())
                <div class="mb-5 bg-red-500/20 border border-red-500/30 rounded-2xl p-4 flex items-start gap-3">
                    <i class="fa-solid fa-circle-exclamation text-red-400 mt-0.5"></i>
                    <p class="text-red-200 text-sm">{{ $errors->first() }}</p>
                </div>
            @endif
            @if(session('error'))
                <div class="mb-5 bg-red-500/20 border border-red-500/30 rounded-2xl p-4 flex items-start gap-3">
                    <i class="fa-solid fa-circle-exclamation text-red-400 mt-0.5"></i>
                    <p class="text-red-200 text-sm">{{ session('error') }}</p>
                </div>
            @endif
            @if(session('success'))
                <div class="mb-5 bg-emerald-500/20 border border-emerald-500/30 rounded-2xl p-4 flex items-start gap-3">
                    <i class="fa-solid fa-circle-check text-emerald-400 mt-0.5"></i>
                    <p class="text-emerald-200 text-sm">{!! session('success') !!}</p>
                </div>
            @endif

            <form action="{{ route('auth.login') }}" method="POST" class="space-y-5">
                @csrf

                {{-- Email --}}
                <div>
                    <label class="block text-white/80 text-sm font-bold mb-2">
                        البريد الإلكتروني أو رقم الهاتف
                    </label>
                    <div class="glass-input-modern rounded-2xl flex items-center px-4 py-3.5">
                        <i class="fa-solid fa-envelope text-gold-400 ml-3"></i>
                        <input type="text"
                               name="login"
                               value="{{ old('login') }}"
                               placeholder="example@email.com أو 07XXXXXXXX"
                               required
                               autofocus
                               class="bg-transparent border-0 outline-none flex-1 text-white placeholder-white/40 font-cairo text-sm">
                    </div>
                </div>

                {{-- Password --}}
                <div x-data="{ show: false }">
                    <label class="block text-white/80 text-sm font-bold mb-2">
                        كلمة المرور
                    </label>
                    <div class="glass-input-modern rounded-2xl flex items-center px-4 py-3.5">
                        <i class="fa-solid fa-lock text-gold-400 ml-3"></i>
                        <input :type="show ? 'text' : 'password'"
                               name="password"
                               placeholder="••••••••"
                               required
                               class="bg-transparent border-0 outline-none flex-1 text-white placeholder-white/40 font-cairo text-sm">
                        <button type="button"
                                @click="show = !show"
                                class="text-white/40 hover:text-gold-400 transition-colors">
                            <i class="fa-solid" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                        </button>
                    </div>
                </div>

                {{-- Options --}}
                <div class="flex items-center justify-between text-sm">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox"
                               name="remember"
                               class="w-4 h-4 rounded border-white/20 bg-white/5 text-gold-500 focus:ring-gold-400 focus:ring-offset-0">
                        <span class="text-white/70">تذكرني</span>
                    </label>
                    <a href="{{ route('auth.password.request') }}"
                       class="text-gold-400 hover:text-gold-300 font-bold transition-colors">
                        نسيت كلمة المرور؟
                    </a>
                </div>

                {{-- Submit --}}
                <button type="submit"
                        class="btn-login-gradient w-full py-4 rounded-2xl text-white font-black text-base flex items-center justify-center gap-3 relative z-10">
                    <i class="fa-solid fa-right-to-bracket"></i>
                    <span>تسجيل الدخول</span>
                </button>
            </form>

            {{-- Footer --}}
            <div class="mt-6 text-center">
                <p class="text-white/60 text-sm">
                    ليس لديك حساب؟
                    <a href="{{ route('auth.register') }}"
                       class="text-gold-400 hover:text-gold-300 font-bold mr-1 transition-colors">
                        سجّل الآن
                    </a>
                </p>
            </div>
        </div>

        {{-- Copyright --}}
        <p class="text-center text-white/30 text-xs mt-6">
            © {{ date('Y') }} {{ config('hirfati.site_name') }} — جميع الحقوق محفوظة
        </p>
    </div>
</div>

@endsection
