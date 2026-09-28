@extends('layouts.craftsman')
@section('title', 'الشكاوى')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl lg:text-3xl font-black text-slate-800">
        <i class="fa-solid fa-triangle-exclamation text-red-500"></i> الشكاوى
    </h1>
</div>

@if($complaints->isEmpty())
    <div class="bg-white rounded-3xl p-12 text-center shadow-soft border border-slate-100">
        <i class="fa-solid fa-face-smile text-5xl text-slate-300 mb-4"></i>
        <p class="text-slate-500">لا توجد شكاوى</p>
    </div>
@else
    <div class="space-y-3">
        @foreach($complaints as $c)
            <div class="bg-white rounded-3xl p-5 shadow-soft border border-slate-100">
                <div class="flex items-center justify-between flex-wrap gap-3 mb-3">
                    <p class="font-bold text-slate-800 text-sm">#{{ str_pad($c->id, 4, '0', STR_PAD_LEFT) }}</p>
                    @php
                        $badges = [
                            'pending' => ['⏳ معلقة', 'amber'],
                            'reviewing' => ['📋 قيد المراجعة', 'blue'],
                            'resolved' => ['✅ تم الحل', 'emerald'],
                            'rejected' => ['❌ مرفوضة', 'slate'],
                        ];
                        $b = $badges[$c->status] ?? ['?', 'slate'];
                    @endphp
                    <span class="bg-{{ $b[1] }}-100 text-{{ $b[1] }}-700 px-3 py-1 rounded-xl text-xs font-bold">{{ $b[0] }}</span>
                </div>
                <div class="grid grid-cols-2 gap-2 text-sm mb-3">
                    <p><span class="text-slate-500">العميل:</span> <strong>{{ $c->client->full_name ?? '' }}</strong></p>
                    <p><span class="text-slate-500">الطلب:</span> <strong>{{ $c->request->title ?? '' }}</strong></p>
                    <p><span class="text-slate-500">السبب:</span> <strong class="text-red-600">{{ $c->reason_label }}</strong></p>
                    <p><span class="text-slate-500">التاريخ:</span> <strong>{{ $c->created_at->format('Y-m-d') }}</strong></p>
                </div>
                <div class="bg-slate-50 rounded-xl p-3 text-sm">{{ $c->details }}</div>
            </div>
        @endforeach
    </div>
@endif
@endsection
