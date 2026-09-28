@extends('layouts.auth')
@section('title', 'إلغاء الدفع')

@section('content')
<div class="min-h-screen flex items-center justify-center p-4">
    <div class="rounded-3xl p-10 max-w-lg w-full text-center"
         style="background: linear-gradient(135deg, rgba(255,255,255,0.08), rgba(255,255,255,0.02)); backdrop-filter: blur(40px); border: 1px solid rgba(255,255,255,0.12);">

        <div class="w-24 h-24 mx-auto rounded-full flex items-center justify-center mb-5"
             style="background: linear-gradient(135deg, #FEE2E2, #EF4444);">
            <i class="fa-solid fa-xmark text-white text-5xl"></i>
        </div>

        <h1 class="text-3xl font-black text-white mb-2">تم إلغاء الدفع</h1>
        <p class="text-white/60 text-sm mb-6">يمكنك المحاولة مرة أخرى</p>

        <a href="{{ route('client.dashboard') }}" class="inline-block px-8 py-3 rounded-2xl text-white font-bold text-sm"
           style="background: linear-gradient(135deg, #D4A24C, #B8873A);">
            <i class="fa-solid fa-home"></i> العودة للرئيسية
        </a>
    </div>
</div>
@endsection
