@extends('layouts.auth')
@section('title', 'دفع ناجح')

@section('content')
<div class="min-h-screen flex items-center justify-center p-4">
    <div class="glass-card rounded-3xl p-10 max-w-lg w-full text-center animate-fade-up"
         style="background: linear-gradient(135deg, rgba(255,255,255,0.08), rgba(255,255,255,0.02)); backdrop-filter: blur(40px); border: 1px solid rgba(255,255,255,0.12);">

        <div class="w-24 h-24 mx-auto rounded-full flex items-center justify-center mb-5"
             style="background: linear-gradient(135deg, #D1FAE5, #10B981);">
            <i class="fa-solid fa-check text-white text-5xl"></i>
        </div>

        <h1 class="text-3xl font-black text-white mb-2">تم الدفع بنجاح! ✅</h1>
        <p class="text-white/60 text-sm mb-6">تم استلام دفعتك وسيتم إشعار الحرفي</p>

        @if($request)
            <div class="bg-white/5 border border-white/10 rounded-2xl p-5 text-right mb-6 space-y-3">
                <div class="flex justify-between text-sm"><span class="text-white/50">رقم العملية</span><strong class="text-white font-mono text-xs">{{ $txn }}</strong></div>
                <div class="flex justify-between text-sm"><span class="text-white/50">رقم الطلب</span><strong class="text-white">#{{ str_pad($request->id, 4, '0', STR_PAD_LEFT) }}</strong></div>
                <div class="flex justify-between text-sm"><span class="text-white/50">الخدمة</span><strong class="text-white truncate">{{ $request->title }}</strong></div>
                <div class="flex justify-between text-sm pt-3 border-t border-white/10">
                    <span class="text-white/50">المبلغ</span>
                    <strong class="text-emerald-400 text-lg">{{ number_format($request->budget ?? 0) }} ر.ي</strong>
                </div>
            </div>
<div class="flex gap-3 justify-center flex-wrap">
    <a href="{{ route('client.invoice', $request->id) }}"
   target="_blank"
   class="px-6 py-3 rounded-2xl text-white font-bold text-sm flex items-center gap-2"
   style="background: linear-gradient(135deg, #10B981, #059669);">
    <i class="fa-solid fa-file-pdf"></i> عرض الفاتورة
</a>

    <a href="{{ route('client.request-details', $request->id) }}"
       class="px-6 py-3 rounded-2xl text-white font-bold text-sm"
       style="background: linear-gradient(135deg, #D4A24C, #B8873A);">
        <i class="fa-solid fa-eye"></i> تفاصيل الطلب
    </a>
    <a href="{{ route('client.dashboard') }}"
       class="px-6 py-3 rounded-2xl bg-white/10 hover:bg-white/20 border border-white/20 text-white font-bold text-sm">
        <i class="fa-solid fa-home"></i> الرئيسية
    </a>
</div>

        @endif
    </div>
</div>
@endsection
