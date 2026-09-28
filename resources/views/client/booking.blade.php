@extends('layouts.client')
@section('title', 'حجز موعد')

@section('content')
<div class="mb-6">
    <a href="{{ route('client.search') }}" class="inline-flex items-center gap-2 text-slate-500 hover:text-gold-600 text-sm font-bold mb-3">
        <i class="fa-solid fa-arrow-right"></i> العودة
    </a>
    <h1 class="text-2xl lg:text-3xl font-black text-slate-800">
        <i class="fa-regular fa-calendar text-gold-500"></i> حجز موعد
    </h1>
    <p class="text-slate-500 text-sm mt-1">مع {{ $craftsman->full_name }}</p>
</div>

<div class="bg-white rounded-3xl p-6 shadow-soft border border-slate-100 max-w-2xl mx-auto">
    <div class="flex items-center justify-between mb-5">
        <a href="{{ route('client.booking', ['id' => $craftsmanId, 'date' => \Carbon\Carbon::parse($date)->subDay()->format('Y-m-d')]) }}"
           class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-sm">
            <i class="fa-solid fa-arrow-right"></i> السابق
        </a>
        <span class="text-lg font-black text-slate-800">{{ \Carbon\Carbon::parse($date)->format('d / m / Y') }}</span>
        <a href="{{ route('client.booking', ['id' => $craftsmanId, 'date' => \Carbon\Carbon::parse($date)->addDay()->format('Y-m-d')]) }}"
           class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-sm">
            التالي <i class="fa-solid fa-arrow-left"></i>
        </a>
    </div>

    <form method="GET" action="{{ route('client.request-form') }}">
        <input type="hidden" name="craftsman_id" value="{{ $craftsmanId }}">
        <input type="hidden" name="preferred_date" value="{{ $date }}">
        <input type="hidden" name="preferred_time" id="selected_time">

        <p class="font-bold text-slate-700 text-sm mb-3">اختر الوقت:</p>
        <div class="grid grid-cols-3 lg:grid-cols-5 gap-2">
            @foreach($availableTimes as $time)
                <button type="button" onclick="selectSlot(this, '{{ $time }}')"
                        class="slot-btn py-3 rounded-2xl border-2 border-slate-200 bg-white hover:border-gold-400 hover:bg-amber-50 font-bold text-sm transition-all">
                    {{ $time }}
                </button>
            @endforeach
        </div>

        <button type="submit" class="w-full mt-6 py-4 rounded-2xl text-white font-black text-base"
                style="background: linear-gradient(135deg, #D4A24C, #B8873A);">
            <i class="fa-regular fa-calendar-check"></i> تأكيد الحجز
        </button>
    </form>
</div>
@endsection

@push('scripts')
<script>
function selectSlot(el, time) {
    document.querySelectorAll('.slot-btn').forEach(b => { b.classList.remove('selected'); b.style.borderColor = '#E2E8F0'; b.style.background = '#FFF'; });
    el.style.borderColor = '#D4A24C'; el.style.background = '#FEF3C7';
    document.getElementById('selected_time').value = time;
}
</script>
@endpush
