@extends('layouts.admin')
@section('title', 'توثيق الحرفيين')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl lg:text-3xl font-black text-slate-800">
        <i class="fa-solid fa-user-check text-gold-500"></i> توثيق الحرفيين
    </h1>
    <p class="text-slate-500 text-sm mt-1">{{ $total_pending }} في الانتظار</p>
</div>

<div class="grid grid-cols-3 gap-3 mb-5">
    <div class="bg-white rounded-2xl p-4 border border-amber-100 shadow-soft text-center">
        <p class="text-2xl font-black text-amber-600">{{ $total_pending }}</p>
        <p class="text-xs text-slate-500 font-bold">⏳ بانتظار</p>
    </div>
    <div class="bg-white rounded-2xl p-4 border border-emerald-100 shadow-soft text-center">
        <p class="text-2xl font-black text-emerald-600">{{ $total_approved }}</p>
        <p class="text-xs text-slate-500 font-bold">✅ معتمدون</p>
    </div>
    <div class="bg-white rounded-2xl p-4 border border-blue-100 shadow-soft text-center">
        <p class="text-2xl font-black text-blue-600">{{ $total_all }}</p>
        <p class="text-xs text-slate-500 font-bold">👤 إجمالي</p>
    </div>
</div>

{{-- Pending --}}
<h3 class="font-black text-slate-800 mb-3 flex items-center gap-2">
    <i class="fa-solid fa-clock text-amber-500"></i> بانتظار التوثيق
</h3>

@if($pending->isEmpty())
    <div class="bg-white rounded-3xl p-8 text-center border border-slate-100 shadow-soft mb-6">
        <p class="text-slate-500">🎉 لا توجد طلبات</p>
    </div>
@else
    <div class="space-y-3 mb-6">
        @foreach($pending as $c)
            <div class="bg-white rounded-3xl p-5 shadow-soft border border-amber-100 flex items-center justify-between flex-wrap gap-4">
                <div class="flex items-center gap-4 flex-1 min-w-[200px]">
                    <img src="{{ $c->avatar_url }}" class="w-14 h-14 rounded-full border-2 border-amber-400 object-cover">
                    <div>
                        <h4 class="font-bold text-slate-800">{{ $c->full_name }}</h4>
                        <p class="text-xs text-slate-500">
                            <i class="fa-solid fa-tag"></i> {{ $c->craftsmanProfile->category->name ?? 'غير محدد' }}
                            · <i class="fa-solid fa-phone"></i> {{ $c->phone }}
                        </p>
                        <p class="text-xs text-slate-400 mt-1">{{ $c->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                <div class="flex gap-2">
                    @if($c->craftsmanProfile->identity_document)
                        <a href="{{ asset('storage/' . $c->craftsmanProfile->identity_document) }}" target="_blank"
                           class="px-4 py-2 rounded-xl bg-blue-100 text-blue-700 font-bold text-xs">
                            <i class="fa-solid fa-file-image"></i> الهوية
                        </a>
                    @endif
                    <form action="{{ route('admin.craftsmen.approve', $c) }}" method="POST">
                        @csrf
                        <button class="px-4 py-2 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-xs">
                            <i class="fa-solid fa-check"></i> اعتماد
                        </button>
                    </form>
                    <form action="{{ route('admin.craftsmen.reject', $c) }}" method="POST">
                        @csrf
                        <button class="px-4 py-2 rounded-xl bg-red-500 hover:bg-red-600 text-white font-bold text-xs" onclick="return confirm('رفض؟')">
                            <i class="fa-solid fa-xmark"></i> رفض
                        </button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
@endif

{{-- Approved --}}
<h3 class="font-black text-slate-800 mb-3 flex items-center gap-2">
    <i class="fa-solid fa-circle-check text-emerald-500"></i> المعتمدون
</h3>

<div class="grid grid-cols-1 md:grid-cols-2 gap-3">
    @foreach($approved as $c)
        <div class="bg-white rounded-3xl p-4 shadow-soft border border-emerald-100 flex items-center justify-between gap-3">
            <div class="flex items-center gap-3 flex-1 min-w-0">
                <img src="{{ $c->avatar_url }}" class="w-12 h-12 rounded-full border-2 border-emerald-400 object-cover">
                <div class="min-w-0">
                    <h4 class="font-bold text-slate-800 text-sm truncate">{{ $c->full_name }}</h4>
                    <p class="text-xs text-slate-500">
                        ⭐ {{ number_format($c->craftsmanProfile->rating_avg ?? 0, 1) }}
                        · {{ $c->craftsmanProfile->category->name ?? '' }}
                    </p>
                </div>
            </div>
            <form action="{{ route('admin.craftsmen.unapprove', $c) }}" method="POST">
                @csrf
                <button class="px-3 py-2 rounded-xl bg-amber-100 text-amber-700 font-bold text-xs" onclick="return confirm('إلغاء الاعتماد؟')">
                    <i class="fa-solid fa-circle-xmark"></i>
                </button>
            </form>
        </div>
    @endforeach
</div>
@endsection
