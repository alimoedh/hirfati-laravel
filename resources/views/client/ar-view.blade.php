@extends('layouts.client')
@section('title', 'الواقع المعزز')

@section('content')
<div class="mb-6">
    <a href="{{ route('client.dashboard') }}" class="inline-flex items-center gap-2 text-slate-500 hover:text-gold-600 text-sm font-bold mb-3">
        <i class="fa-solid fa-arrow-right"></i> العودة
    </a>
    <h1 class="text-2xl lg:text-3xl font-black text-slate-800">
        <i class="fa-solid fa-cube text-gold-500"></i> الواقع المعزز
    </h1>
</div>

<div class="bg-white rounded-3xl p-6 shadow-soft border border-slate-100">
    <div class="bg-blue-50 border-r-4 border-blue-400 rounded-2xl p-4 mb-4 text-sm text-blue-800">
        <strong><i class="fa-solid fa-info-circle"></i> كيف يعمل؟</strong>
        <ul class="mt-2 space-y-1 text-xs">
            <li><i class="fa-solid fa-camera"></i> استخدم كاميرا هاتفك لتحديد موقع المشكلة</li>
            <li><i class="fa-solid fa-crosshairs"></i> وجه الكاميرا نحو مكان المشكلة</li>
            <li><i class="fa-solid fa-share"></i> أرسل الموقع للحرفي</li>
        </ul>
    </div>

    <div class="flex gap-3 flex-wrap mb-4">
        <button onclick="showToast('تم تشغيل AR','success')" class="px-5 py-3 rounded-2xl text-white font-bold text-sm"
                style="background: linear-gradient(135deg, #D4A24C, #B8873A);">
            <i class="fa-solid fa-play"></i> بدء AR
        </button>
        <button onclick="showToast('تم التقاط الصورة','success')" class="px-5 py-3 rounded-2xl bg-emerald-500 text-white font-bold text-sm">
            <i class="fa-solid fa-camera"></i> التقاط
        </button>
    </div>

    <div class="rounded-3xl overflow-hidden bg-black" style="height: 60vh;">
        <a-scene embedded arjs="sourceType: webcam; debugUIEnabled: false;">
            <a-marker preset="hiro"><a-box position="0 0.5 0" material="color: #D4A24C;"></a-box></a-marker>
            <a-entity camera></a-entity>
        </a-scene>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/aframe@1.3.0/dist/aframe.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/ar.js@2.2.2/aframe/build/aframe-ar.min.js"></script>
@endpush
