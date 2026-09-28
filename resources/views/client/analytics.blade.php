@extends('layouts.client')
@section('title', 'التحليلات')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl lg:text-3xl font-black text-slate-800">
        <i class="fa-solid fa-chart-simple text-gold-500"></i> التحليلات
    </h1>
</div>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    @foreach([
        ['total','إجمالي الطلبات','fa-list','blue'],
        ['completed','مكتملة','fa-circle-check','emerald'],
        ['pending','قيد الانتظار','fa-clock','amber'],
        ['in_progress','قيد التنفيذ','fa-spinner','purple'],
    ] as $s)
        <div class="bg-white rounded-3xl p-5 shadow-soft border border-slate-100 text-center">
            <div class="w-12 h-12 mx-auto rounded-2xl bg-{{ $s[3] }}-100 text-{{ $s[3] }}-600 flex items-center justify-center mb-3">
                <i class="fa-solid {{ $s[2] }} text-xl"></i>
            </div>
            <p class="text-3xl font-black text-{{ $s[3] }}-600">{{ $stats[$s[0]] }}</p>
            <p class="text-xs text-slate-500 font-bold">{{ $s[1] }}</p>
        </div>
    @endforeach
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
    @if(!empty($mLabels))
        <div class="bg-white rounded-3xl p-6 shadow-soft border border-slate-100">
            <h3 class="font-black text-slate-800 mb-4">📅 الطلبات آخر 6 أشهر</h3>
            <canvas id="monthlyChart"></canvas>
        </div>
    @endif

    <div class="bg-white rounded-3xl p-6 shadow-soft border border-slate-100">
        <h3 class="font-black text-slate-800 mb-4">📊 توزيع الحالات</h3>
        <canvas id="statusChart"></canvas>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
@if(!empty($mLabels))
new Chart(document.getElementById('monthlyChart'), {
    type: 'bar',
    data: { labels: @json($mLabels), datasets: [{ data: @json($mCounts), backgroundColor: 'rgba(212,162,76,0.7)', borderRadius: 12, barThickness: 30 }] },
    options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
});
@endif
new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: { labels: ['مكتمل','قيد الانتظار','جاري التنفيذ','ملغي'], datasets: [{ data: @json($statusData), backgroundColor: ['#10B981','#F59E0B','#3B82F6','#EF4444'], borderWidth: 3, borderColor: '#FFF' }] },
    options: { cutout: '65%', plugins: { legend: { position: 'bottom' } } }
});
</script>
@endpush
