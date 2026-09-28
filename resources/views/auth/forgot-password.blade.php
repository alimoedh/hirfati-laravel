@extends('layouts.auth')
@section('title', 'استعادة كلمة المرور')

@section('content')
<div class="min-h-screen flex items-center justify-center p-4">
    <div class="rounded-3xl p-8 max-w-md w-full"
         style="background: linear-gradient(135deg, rgba(255,255,255,0.08), rgba(255,255,255,0.02)); backdrop-filter: blur(40px); border: 1px solid rgba(255,255,255,0.12);">

        <div class="text-center mb-6">
            <img src="{{ asset('assets/images/logo.png') }}" class="w-32 mx-auto mb-4">
            <h1 class="text-2xl font-black text-white mb-2">استعادة كلمة المرور</h1>
            <p class="text-white/60 text-sm">أدخل بريدك الإلكتروني</p>
        </div>

        @if(session('error'))
            <div class="mb-4 bg-red-500/20 border border-red-500/30 rounded-2xl p-3 text-red-200 text-sm">{{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="mb-4 bg-red-500/20 border border-red-500/30 rounded-2xl p-3 text-red-200 text-sm">{{ $errors->first() }}</div>
        @endif

        @if(session('success'))
            <div class="mb-4 bg-emerald-500/20 border border-emerald-500/30 rounded-2xl p-3 text-emerald-200 text-sm">{!! session('success') !!}</div>
        @else
            <form method="POST" action="{{ route('auth.password.email') }}" class="space-y-4">
                @csrf
                <div class="rounded-2xl flex items-center px-4 py-3.5"
                     style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);">
                    <i class="fa-solid fa-envelope text-gold-400 ml-3"></i>
                    <input type="email" name="email" placeholder="example@email.com" required
                           class="bg-transparent border-0 outline-none flex-1 text-white placeholder-white/40 font-cairo text-sm">
                </div>
                <button type="submit" class="w-full py-4 rounded-2xl text-white font-black text-sm"
                        style="background: linear-gradient(135deg, #D4A24C, #B8873A);">
                    <i class="fa-solid fa-paper-plane"></i> إرسال الرابط
                </button>
            </form>
        @endif

        <div class="text-center mt-6">
            <a href="{{ route('auth.login') }}" class="text-gold-400 hover:text-gold-300 text-sm font-bold">
                <i class="fa-solid fa-arrow-right"></i> العودة لتسجيل الدخول
            </a>
        </div>
    </div>
</div>
@endsection
