@extends('layouts.client')
@section('title', 'الفيديوهات التعليمية')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl lg:text-3xl font-black text-slate-800">
        <i class="fa-solid fa-graduation-cap text-gold-500"></i> الفيديوهات التعليمية
    </h1>
</div>

<div class="flex flex-wrap gap-2 mb-5">
    <a href="{{ route('client.tutorials') }}" class="px-4 py-2 rounded-2xl border-2 {{ !$categoryId ? 'bg-brand-800 text-white' : 'bg-white text-slate-600' }} font-bold text-sm">
        الكل
    </a>
    @foreach($categories as $cat)
        <a href="{{ route('client.tutorials', ['category' => $cat->id]) }}"
           class="px-4 py-2 rounded-2xl border-2 {{ $categoryId == $cat->id ? 'bg-brand-800 text-white' : 'bg-white text-slate-600' }} font-bold text-sm">
            {{ $cat->name }}
        </a>
    @endforeach
</div>

@if($tutorials->isEmpty())
    <div class="bg-white rounded-3xl p-12 text-center shadow-soft border border-slate-100">
        <i class="fa-solid fa-video text-5xl text-slate-300 mb-4"></i>
        <p class="text-slate-500">لا توجد فيديوهات حالياً</p>
    </div>
@else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @foreach($tutorials as $tut)
            <div onclick="window.open('{{ $tut->video_url }}', '_blank')"
                 class="bg-white rounded-3xl overflow-hidden shadow-soft border border-slate-100 hover:shadow-lift hover:-translate-y-1 transition-all cursor-pointer">
                <div class="h-40 bg-gradient-to-br from-brand-800 to-brand-600 flex items-center justify-center relative">
                    <div class="w-16 h-16 rounded-full bg-white/20 backdrop-blur flex items-center justify-center">
                        <i class="fa-solid fa-play text-white text-2xl"></i>
                    </div>
                </div>
                <div class="p-5">
                    <h3 class="font-bold text-slate-800 mb-2">{{ $tut->title }}</h3>
                    <p class="text-xs text-slate-500 line-clamp-2">{{ $tut->description }}</p>
                </div>
            </div>
        @endforeach
    </div>
@endif
@endsection
