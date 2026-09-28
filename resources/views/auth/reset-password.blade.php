@extends('layouts.auth')
@section('title', 'إعادة تعيين كلمة المرور')

@section('content')
<div class="min-h-screen flex items-center justify-center p-4">
    <div class="rounded-3xl p-8 max-w-md w-full"
         style="background: linear-gradient(135deg, rgba(255,255,255,0.08), rgba(255,255,255,0.02)); backdrop-filter: blur(40px); border: 1px solid rgba(255,255,255,0.12);">

        <div class="text-center mb-6">
            <img src="{{ asset('assets/images/logo.png') }}" class="w-32 mx-auto mb-4">
            <h1 class="text-2xl font-black text-white mb-2">كلمة مرور جديدة</h1>
        </div>

        @if(session('error') || isset($error))
            <div class="mb-4 bg-red-500/20 border border-red-500/30 rounded-2xl p-3 text-red-200 text-sm">{{ session('error') ?? $error }}</div>
        @endif
        @if($errors->any())
            <div class="mb-4 bg-red-500/20 border border-red-500/30 rounded-2xl p-3 text-red-200 text-sm">{{ $errors->first() }}</div>
        @endif

        @if($token)
            <form method="POST" action="{{ route('auth.password.update') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                @foreach([['new_password','كلمة المرور الجديدة'],['confirm_password','تأكيد كلمة المرور']] as $f)
                    <div class="rounded-2xl flex items-center px-4 py-3.5"
                         style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);">
                        <i class="fa-solid fa-lock text-gold-400 ml-3"></i>
                        <input type="password" name="{{ $f[0] }}" placeholder="{{ $f[1] }}" required
                               class="bg-transparent border-0 outline-none flex-1 text-white placeholder-white/40 font-cairo text-sm">
                    </div>
                @endforeach
                <button type="submit" class="w-full py-4 rounded-2xl text-white font-black text-sm"
                        style="background: linear-gradient(135deg, #D4A24C, #B8873A);">
                    <i class="fa-solid fa-check"></i> تعيين كلمة المرور
                </button>
            </form>
        @else
            <div class="text-center py-6">
                <i class="fa-solid fa-circle-xmark text-5xl text-red-400 mb-3"></i>
                <p class="text-white/70">الرابط غير صالح</p>
            </div>
        @endif

        <div class="text-center mt-6">
            <a href="{{ route('auth.login') }}" class="text-gold-400 hover:text-gold-300 text-sm font-bold">
                <i class="fa-solid fa-arrow-right"></i> العودة لتسجيل الدخول
            </a>
        </div>
    </div>
</div>
@endsection
