@extends('layouts.auth')
@section('title', 'التحقق بخطوتين')

@section('content')
<div class="min-h-screen flex items-center justify-center p-4">
    <div class="rounded-3xl p-8 max-w-md w-full text-center"
         style="background: linear-gradient(135deg, rgba(255,255,255,0.08), rgba(255,255,255,0.02)); backdrop-filter: blur(40px); border: 1px solid rgba(255,255,255,0.12);">

        <i class="fa-solid fa-shield-halved text-5xl text-gold-400 mb-4"></i>
        <h1 class="text-2xl font-black text-white mb-2">التحقق بخطوتين</h1>
        <p class="text-white/60 text-sm mb-6">امسح الرمز باستخدام Google Authenticator</p>

        @if(session('error'))
            <div class="mb-4 bg-red-500/20 border border-red-500/30 rounded-2xl p-3 text-red-200 text-sm">{{ session('error') }}</div>
        @endif

        <div class="bg-white rounded-2xl p-4 inline-block mb-6">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode($qrCodeUrl) }}" alt="QR" class="w-48 h-48">
        </div>

        <form method="POST" action="{{ route('auth.2fa') }}" class="space-y-4">
            @csrf
            <input type="text" name="code" placeholder="000000" maxlength="6" required autofocus
                   class="w-full px-4 py-4 rounded-2xl text-center text-2xl font-black tracking-widest"
                   style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: white;">
            <button type="submit" class="w-full py-4 rounded-2xl text-white font-black text-sm"
                    style="background: linear-gradient(135deg, #D4A24C, #B8873A);">
                تحقق
            </button>
        </form>
    </div>
</div>
@endsection
