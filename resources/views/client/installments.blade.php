@extends('layouts.client')
@section('title', 'الأقساط')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl lg:text-3xl font-black text-slate-800">
        <i class="fa-solid fa-credit-card text-gold-500"></i> الأقساط
    </h1>
</div>

{{-- Hero --}}
<div class="rounded-3xl p-6 mb-5 text-white" style="background: linear-gradient(135deg, #2563EB, #1E40AF);">
    <p class="text-sm opacity-80 mb-1">الرصيد المتبقي</p>
    <p class="text-4xl font-black">{{ number_format($stats['remaining'], 2) }} <span class="text-sm">ر.ي</span></p>
</div>

{{-- Stats --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
    @foreach([
        ['total','إجمالي الأقساط','fa-list','slate'],
        ['paid','مدفوعة','fa-circle-check','emerald'],
        ['pending','معلقة','fa-clock','amber'],
        ['overdue','متأخرة','fa-exclamation-triangle','red'],
    ] as $s)
        <div class="bg-white rounded-2xl p-4 border border-slate-100 shadow-soft text-center">
            <i class="fa-solid {{ $s[2] }} text-{{ $s[3] }}-500 text-xl mb-2"></i>
            <p class="text-2xl font-black text-{{ $s[3] }}-600">{{ $stats[$s[0]] }}</p>
            <p class="text-xs text-slate-500 font-bold">{{ $s[1] }}</p>
        </div>
    @endforeach
</div>

@if($installments->isEmpty())
    <div class="bg-white rounded-3xl p-12 text-center shadow-soft border border-slate-100">
        <i class="fa-solid fa-credit-card text-5xl text-slate-300 mb-4"></i>
        <h3 class="font-bold text-slate-700 mb-2">لا توجد أقساط</h3>
        <p class="text-sm text-slate-500">لم تقم بأي طلب بالتقسيط</p>
    </div>
@else
    <div class="space-y-3">
        @foreach($installments as $inst)
            @php
                $isOverdue = $inst->is_overdue;
                $statusClass = $inst->status === 'paid' ? 'paid' : ($isOverdue ? 'overdue' : 'pending');
                $colors = ['paid' => 'emerald', 'overdue' => 'red', 'pending' => 'amber'];
                $c = $colors[$statusClass];
            @endphp
            <div class="bg-white rounded-3xl p-4 border-2 border-{{ $c }}-100 shadow-soft hover:shadow-lift transition-all">
                <div class="flex items-center gap-4 flex-wrap">
                    <div class="w-14 h-14 rounded-2xl bg-{{ $c }}-100 text-{{ $c }}-600 flex items-center justify-center font-black text-lg">
                        {{ $inst->current_installment }}
                    </div>
                    <div class="flex-1 min-w-[150px]">
                        <p class="font-bold text-slate-800 text-sm">القسط {{ $inst->current_installment }} من {{ $inst->installment_count }}</p>
                        <p class="text-xs text-slate-500 mt-1">#{{ str_pad($inst->request_id, 4, '0', STR_PAD_LEFT) }} — {{ $inst->request->title ?? '' }}</p>
                        <p class="text-xs text-slate-400 mt-0.5"><i class="fa-regular fa-calendar"></i> {{ $inst->due_date->format('Y-m-d') }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-xl font-black text-slate-800">{{ number_format($inst->per_installment_amount, 2) }}</p>
                        <p class="text-xs text-slate-400">ريال</p>
                    </div>
                    @if($inst->status !== 'paid')
                        <form method="POST" action="{{ route('client.installments.pay', $inst) }}">
                            @csrf
                            <button class="px-4 py-2 rounded-xl text-white font-bold text-xs"
                                    style="background: linear-gradient(135deg, #10B981, #059669);">
                                <i class="fa-solid fa-credit-card"></i> ادفع
                            </button>
                        </form>
                    @else
                        <span class="text-emerald-600 font-bold text-xs"><i class="fa-solid fa-check-circle"></i> مدفوع</span>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
@endif
@endsection
