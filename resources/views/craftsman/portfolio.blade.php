@extends('layouts.craftsman')
@section('title', 'معرض أعمالي')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl lg:text-3xl font-black text-slate-800">
        <i class="fa-solid fa-images text-gold-500"></i> معرض أعمالي
    </h1>
</div>

<div class="grid grid-cols-3 gap-3 mb-5">
    <div class="bg-white rounded-2xl p-4 border border-slate-100 shadow-soft text-center">
        <p class="text-2xl font-black text-slate-800">{{ $completedWorks->count() }}</p>
        <p class="text-xs text-slate-500 font-bold">أعمال مكتملة</p>
    </div>
    <div class="bg-white rounded-2xl p-4 border border-slate-100 shadow-soft text-center">
        <p class="text-2xl font-black text-amber-600">{{ number_format($rating['avg'], 1) }} ⭐</p>
        <p class="text-xs text-slate-500 font-bold">متوسط التقييم</p>
    </div>
    <div class="bg-white rounded-2xl p-4 border border-slate-100 shadow-soft text-center">
        <p class="text-2xl font-black text-emerald-600">{{ $rating['total'] }}</p>
        <p class="text-xs text-slate-500 font-bold">عدد التقييمات</p>
    </div>
</div>

@if($completedWorks->isEmpty())
    <div class="bg-white rounded-3xl p-12 text-center shadow-soft border border-slate-100">
        <i class="fa-solid fa-image text-5xl text-slate-300 mb-4"></i>
        <p class="text-slate-500">لا توجد أعمال مكتملة بعد</p>
    </div>
@else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @foreach($completedWorks as $w)
            <div class="bg-white rounded-3xl overflow-hidden shadow-soft border border-slate-100 hover:shadow-lift transition-all">
                @if($w->problem_image)
                    <img src="{{ $w->problem_image_url }}" class="w-full h-40 object-cover">
                @else
                    <div class="w-full h-40 bg-gradient-to-br from-slate-100 to-slate-200 flex items-center justify-center">
                        <i class="fa-solid fa-image text-4xl text-slate-400"></i>
                    </div>
                @endif
                <div class="p-5">
                    <h3 class="font-bold text-slate-800 mb-2">{{ $w->title }}</h3>
                    <p class="text-xs text-slate-500"><i class="fa-regular fa-user"></i> {{ $w->client->full_name ?? '' }}</p>
                    <p class="text-xs text-slate-400 mt-1">{{ $w->created_at->format('Y-m-d') }}</p>
                </div>
            </div>
        @endforeach
    </div>
@endif
@endsection
