@extends('layouts.client')
@section('title', 'التقارير')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl lg:text-3xl font-black text-slate-800">
        <i class="fa-solid fa-chart-line text-gold-500"></i> التقارير
    </h1>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
    <div class="bg-white rounded-3xl p-6 shadow-soft border border-slate-100 text-center">
        <i class="fa-solid fa-clipboard-list text-4xl text-blue-500 mb-3"></i>
        <p class="text-4xl font-black text-slate-800">{{ $reports['total_requests'] }}</p>
        <p class="text-sm text-slate-500 font-bold">إجمالي الطلبات</p>
    </div>
    <div class="bg-white rounded-3xl p-6 shadow-soft border border-slate-100 text-center">
        <i class="fa-solid fa-circle-check text-4xl text-emerald-500 mb-3"></i>
        <p class="text-4xl font-black text-slate-800">{{ $reports['completed_requests'] }}</p>
        <p class="text-sm text-slate-500 font-bold">طلبات مكتملة</p>
    </div>
</div>

<div class="flex flex-wrap gap-3">
    <a href="{{ route('client.reports.export', ['type' => 'csv']) }}" class="px-6 py-3 rounded-2xl text-white font-bold text-sm"
       style="background: linear-gradient(135deg, #D4A24C, #B8873A);">
        <i class="fa-solid fa-file-csv"></i> تصدير CSV
    </a>
    <button onclick="window.print()" class="px-6 py-3 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-sm">
        <i class="fa-solid fa-print"></i> طباعة
    </button>
</div>
@endsection
