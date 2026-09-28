@extends('layouts.auth')
@section('title', 'التحقق من الهوية')

@section('content')
<div class="min-h-screen flex items-center justify-center p-4">
    <div class="rounded-3xl p-8 max-w-md w-full"
         style="background: linear-gradient(135deg, rgba(255,255,255,0.08), rgba(255,255,255,0.02)); backdrop-filter: blur(40px); border: 1px solid rgba(255,255,255,0.12);">

        <div class="text-center mb-6">
            <i class="fa-solid fa-shield-halved text-5xl text-gold-400 mb-3"></i>
            <h1 class="text-2xl font-black text-white">التحقق من الهوية</h1>
        </div>

        @if(session('success'))
            <div class="mb-4 bg-emerald-500/20 border border-emerald-500/30 rounded-2xl p-3 text-emerald-200 text-sm">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="mb-4 bg-red-500/20 border border-red-500/30 rounded-2xl p-3 text-red-200 text-sm">{{ session('error') }}</div>
        @endif

        @if(!$user->is_verified)
            <form method="POST" action="{{ route('auth.verify.send') }}" class="space-y-4 mb-5">
                @csrf
                <select name="verification_type" class="w-full px-4 py-3 rounded-2xl text-white"
                        style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);">
                    <option value="phone" class="bg-brand-800">📱 التحقق عبر الهاتف</option>
                    <option value="email" class="bg-brand-800">📧 التحقق عبر البريد</option>
                </select>
                <input type="tel" name="phone" value="{{ $user->phone }}" required placeholder="رقم الهاتف"
                       class="w-full px-4 py-3 rounded-2xl text-white placeholder-white/40"
                       style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);">
                <button type="submit" class="w-full py-3 rounded-2xl text-white font-bold text-sm"
                        style="background: linear-gradient(135deg, #D4A24C, #B8873A);">
                    <i class="fa-solid fa-paper-plane"></i> إرسال الرمز
                </button>
            </form>

            <div class="border-t border-white/10 pt-5">
                <form method="POST" action="{{ route('auth.verify') }}" class="space-y-4">
                    @csrf
                    <input type="text" name="code" placeholder="000000" maxlength="6" required
                           class="w-full px-4 py-3 rounded-2xl text-center text-xl font-black text-white tracking-widest"
                           style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);">
                    <button type="submit" class="w-full py-3 rounded-2xl bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-sm">
                        <i class="fa-solid fa-circle-check"></i> تحقق
                    </button>
                </form>
            </div>
        @else
            <div class="text-center py-6">
                <i class="fa-solid fa-circle-check text-6xl text-emerald-400 mb-3"></i>
                <h3 class="text-xl font-black text-white">حسابك موثق!</h3>
                <p class="text-white/60 text-sm mt-2">تم التحقق من هويتك بنجاح</p>
            </div>
        @endif
    </div>
</div>
@endsection
