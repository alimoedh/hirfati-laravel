@extends('layouts.client')
@section('title', 'البث المباشر')

@section('content')
<div class="mb-6">
    <a href="{{ $backUrl }}" class="inline-flex items-center gap-2 text-slate-500 hover:text-gold-600 text-sm font-bold mb-3">
        <i class="fa-solid fa-arrow-right"></i> العودة
    </a>
</div>

@if(!$request)
    <div class="bg-amber-50 border-2 border-amber-200 rounded-3xl p-12 text-center">
        <i class="fa-solid fa-video-slash text-5xl text-amber-500 mb-4"></i>
        <h3 class="font-bold text-amber-800 mb-2">لم يتم تحديد طلب</h3>
        <p class="text-sm text-amber-700 mb-4">افتح البث من صفحة تفاصيل الطلب</p>
        <a href="{{ $backUrl }}" class="inline-block px-6 py-3 rounded-2xl text-white font-bold text-sm"
           style="background: linear-gradient(135deg, #D4A24C, #B8873A);">
            العودة
        </a>
    </div>
@else
    <div class="rounded-3xl p-5 mb-4 text-white" style="background: linear-gradient(135deg, #DC2626, #991B1B);">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div>
                <h2 class="font-black text-xl"><i class="fa-solid fa-video"></i> بث مباشر</h2>
                <p class="text-sm opacity-80 mt-1">الطلب #{{ str_pad($request->id, 4, '0', STR_PAD_LEFT) }} — {{ $request->title }}</p>
            </div>
            <span class="inline-flex items-center gap-2 bg-white/20 px-4 py-2 rounded-xl text-xs font-bold">
                <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></span> غرفة نشطة
            </span>
        </div>
    </div>

    <div class="rounded-3xl overflow-hidden shadow-lift bg-black" style="height: 600px;">
        <iframe src="https://meet.jit.si/{{ $roomName }}#userInfo.displayName=%22{{ urlencode(auth()->user()->full_name) }}%22&config.prejoinPageEnabled=false"
                allow="camera; microphone; fullscreen; display-capture; autoplay"
                allowfullscreen style="width:100%; height:100%; border:none;"></iframe>
    </div>

    <div class="flex flex-wrap gap-3 mt-4">
        <button onclick="navigator.share?.({title:'بث مباشر', url:'https://meet.jit.si/{{ $roomName }}'}) || navigator.clipboard.writeText('https://meet.jit.si/{{ $roomName }}')"
                class="px-5 py-3 rounded-2xl bg-blue-500 hover:bg-blue-600 text-white font-bold text-sm">
            <i class="fa-solid fa-share-nodes"></i> مشاركة
        </button>
        <a href="{{ $backUrl }}" class="px-5 py-3 rounded-2xl bg-red-500 hover:bg-red-600 text-white font-bold text-sm">
            <i class="fa-solid fa-right-from-bracket"></i> خروج
        </a>
    </div>
@endif
@endsection
