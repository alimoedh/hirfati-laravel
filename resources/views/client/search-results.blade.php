@extends('layouts.client')
@section('title', 'نتائج البحث')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl lg:text-3xl font-black text-slate-800">
        <i class="fa-solid fa-magnifying-glass text-gold-500"></i>
        البحث عن حرفي
    </h1>
    <p class="text-slate-500 text-sm mt-1">{{ $craftsmen->total() }} نتيجة</p>
</div>

{{-- Search Bar --}}
<form method="GET" class="bg-white rounded-3xl p-2 shadow-soft border border-slate-100 flex gap-2 mb-5">
    <input type="text" name="q" value="{{ $search }}" placeholder="🔍 ابحث عن حرفي..." class="flex-1 bg-transparent border-0 outline-none px-4 py-3 text-slate-800 font-cairo text-sm">
    <input type="hidden" name="category" value="{{ $categoryId }}">
    <button type="submit" class="px-6 py-3 bg-gradient-to-br from-gold-400 to-gold-600 text-white font-bold rounded-2xl hover:shadow-glow transition-all">
        <i class="fa-solid fa-search"></i>
    </button>
</form>

{{-- Category Filters --}}
<div class="flex flex-wrap gap-2 mb-6">
    <a href="{{ route('client.search', ['q' => $search]) }}" class="px-4 py-2 rounded-2xl border-2 {{ !$categoryId ? 'bg-brand-800 text-white border-brand-800 shadow-lg' : 'bg-white text-slate-600 border-slate-200 hover:border-gold-300' }} font-bold text-sm transition-all">
        الكل
    </a>
    @foreach($categories as $cat)
        <a href="{{ route('client.search', ['q' => $search, 'category' => $cat->id]) }}"
           class="px-4 py-2 rounded-2xl border-2 {{ $categoryId == $cat->id ? 'bg-brand-800 text-white border-brand-800 shadow-lg' : 'bg-white text-slate-600 border-slate-200 hover:border-gold-300' }} font-bold text-sm transition-all flex items-center gap-1.5">
            <i class="fa-solid {{ $cat->icon }} text-xs"></i>
            {{ $cat->name }}
        </a>
    @endforeach
</div>

{{-- Results --}}
@if($craftsmen->isEmpty())
    <div class="bg-white rounded-3xl p-16 text-center shadow-soft border border-slate-100">
        <i class="fa-solid fa-face-frown text-5xl text-slate-300 mb-4"></i>
        <h3 class="font-bold text-slate-700 text-lg mb-2">لا توجد نتائج</h3>
        <p class="text-sm text-slate-500">لم نجد أي حرفي يطابق معايير البحث</p>
    </div>
@else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @foreach($craftsmen as $c)
            @php $profile = $c->craftsmanProfile; @endphp
            <div class="bg-white rounded-3xl overflow-hidden shadow-soft border border-slate-100 hover:shadow-lift transition-all">
                {{-- Gradient Header --}}
                <div class="h-20 bg-gradient-to-br from-brand-800 to-brand-600 relative">
                    <div class="absolute -bottom-10 right-6">
                        <img src="{{ $c->avatar_url }}" class="w-20 h-20 rounded-2xl border-4 border-white object-cover shadow-lg">
                    </div>
                </div>

                <div class="p-6 pt-14">
                    <div class="flex items-start justify-between mb-3">
                        <div class="min-w-0 flex-1">
                            <h3 class="font-bold text-slate-800 truncate">{{ $c->full_name }}</h3>
                            <p class="text-xs text-slate-500 mt-1">{{ $profile->category->name ?? 'غير محدد' }}</p>
                        </div>
                        <div class="flex items-center gap-1 bg-amber-100 px-2 py-1 rounded-lg">
                            <i class="fa-solid fa-star text-amber-500 text-xs"></i>
                            <span class="text-xs font-bold text-amber-700">{{ number_format($profile->rating_avg ?? 0, 1) }}</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 text-xs text-slate-500 mb-3">
                        <i class="fa-solid fa-briefcase"></i>
                        <span>{{ $profile->experience_years ?? 0 }} سنوات خبرة</span>
                    </div>

                    @if($profile->is_available)
                        <div class="inline-flex items-center gap-1.5 bg-emerald-100 text-emerald-700 px-3 py-1 rounded-xl text-xs font-bold mb-4">
                            <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                            متوفر
                        </div>
                    @endif

                    <div class="grid grid-cols-2 gap-2">
                        <a href="{{ route('client.request-form') }}?craftsman_id={{ $c->id }}"
                           class="col-span-2 text-center py-2.5 rounded-xl bg-gradient-to-br from-gold-400 to-gold-600 text-white font-bold text-sm hover:shadow-glow transition-all">
                            <i class="fa-solid fa-paper-plane ml-1"></i> طلب الخدمة
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @if($craftsmen->hasPages())
        <div class="mt-6 flex justify-center">
            {{ $craftsmen->links() }}
        </div>
    @endif
@endif
@endsection
