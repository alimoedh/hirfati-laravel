@extends('layouts.craftsman')
@section('title', 'التقييمات')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl lg:text-3xl font-black text-slate-800">
        <i class="fa-solid fa-star text-gold-500"></i> تقييماتي
    </h1>
</div>

@if($total == 0)
    <div class="bg-white rounded-3xl p-12 text-center shadow-soft border border-slate-100">
        <i class="fa-regular fa-star text-5xl text-slate-300 mb-4"></i>
        <h3 class="font-bold text-slate-700 mb-2">لا توجد تقييمات بعد</h3>
        <p class="text-sm text-slate-500">عندما يُقيّم العملاء خدماتك ستظهر هنا</p>
    </div>
@else
    <div class="rounded-3xl p-6 mb-5 text-white" style="background: linear-gradient(135deg, #1E3A5F, #2563EB);">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <p class="text-sm opacity-70 mb-1">متوسط التقييم</p>
                <div class="flex items-baseline gap-2">
                    <span class="text-5xl font-black">{{ number_format($avgRating, 1) }}</span>
                    <span class="text-2xl">⭐</span>
                </div>
                <p class="text-sm opacity-80 mt-1">{{ $total }} تقييم</p>
            </div>
            <div class="grid grid-cols-3 gap-3">
                @foreach([['🎨',$avgQuality,'جودة'],['⏰',$avgPunctuality,'التزام'],['🤝',$avgBehavior,'تعامل']] as $c)
                    <div class="bg-white/10 rounded-2xl p-3 text-center">
                        <div class="text-xl mb-1">{{ $c[0] }}</div>
                        <div class="text-lg font-black">{{ number_format($c[1], 1) }}</div>
                        <div class="text-[10px] opacity-70">{{ $c[2] }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="bg-white rounded-3xl p-6 shadow-soft border border-slate-100 mb-5">
        <h3 class="font-black text-slate-800 mb-4">📊 توزيع التقييمات</h3>
        <canvas id="ratingsChart" height="100"></canvas>
    </div>

    <div class="space-y-3">
        @foreach($reviews as $rv)
            <div class="bg-white rounded-3xl p-5 shadow-soft border border-slate-100">
                <div class="flex items-start justify-between gap-3 flex-wrap mb-3">
                    <div class="flex items-center gap-3">
                        <img src="{{ $rv->client->avatar_url ?? asset('assets/images/logo.png') }}" class="w-12 h-12 rounded-full border-2 border-gold-400 object-cover">
                        <div>
                            <p class="font-bold text-slate-800 text-sm">{{ $rv->client->full_name }}</p>
                            <p class="text-xs text-slate-500">{{ $rv->request->title ?? '' }}</p>
                        </div>
                    </div>
                    <div class="text-left">
                        <div class="text-lg text-gold-500">
                            @for($i = 1; $i <= 5; $i++) {{ $i <= $rv->rating ? '★' : '☆' }} @endfor
                        </div>
                        <p class="text-xs text-slate-400 mt-1">{{ $rv->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                @if($rv->comment)
                    <div class="bg-slate-50 border-r-3 border-gold-400 rounded-xl p-3 text-sm">
                        {{ $rv->comment }}
                    </div>
                @endif
            </div>
        @endforeach
    </div>
@endif
@endsection

@push('scripts')
@if($total > 0)
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
new Chart(document.getElementById('ratingsChart'), {
    type: 'bar',
    data: {
        labels: ['⭐ 1','⭐ 2','⭐ 3','⭐ 4','⭐ 5'],
        datasets: [{
            data: [{{ $distribution[1] }},{{ $distribution[2] }},{{ $distribution[3] }},{{ $distribution[4] }},{{ $distribution[5] }}],
            backgroundColor: ['#EF4444','#F97316','#F59E0B','#84CC16','#10B981'],
            borderRadius: 12, barThickness: 40
        }]
    },
    options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
});
</script>
@endif
@endpush
