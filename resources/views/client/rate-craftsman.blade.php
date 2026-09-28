@extends('layouts.client')
@section('title', 'تقييم الحرفي')

@push('styles')
<style>
    .star-btn { font-size: 2.5rem; color: #CBD5E1; cursor: pointer; transition: all 0.2s; }
    .star-btn:hover, .star-btn.active { color: #D4A24C; transform: scale(1.15); }
</style>
@endpush

@section('content')
<div class="bg-white rounded-3xl p-8 shadow-soft border border-slate-100 max-w-xl mx-auto text-center">
    <h1 class="text-2xl font-black text-slate-800 mb-2">⭐ تقييم الحرفي</h1>
    <p class="text-sm text-slate-500 mb-6">شاركنا رأيك لتساعد الآخرين</p>

    <div class="flex items-center justify-center gap-3 mb-6">
        <img src="{{ $request->craftsman->avatar_url }}" class="w-16 h-16 rounded-full border-4 border-gold-400 object-cover">
        <div class="text-right">
            <h3 class="font-bold text-slate-800">{{ $request->craftsman->full_name }}</h3>
            <p class="text-xs text-slate-500">الطلب #{{ str_pad($request->id, 4, '0', STR_PAD_LEFT) }}</p>
        </div>
    </div>

    <form method="POST" action="{{ route('client.rate-craftsman.store', $request->id) }}" class="space-y-5">
        @csrf
        <input type="hidden" name="rating" id="ratingInput" required>

        <div>
            <p class="font-bold text-slate-700 mb-3">التقييم العام *</p>
            <div class="flex justify-center gap-2" id="mainStars" dir="ltr">
                @for($i = 1; $i <= 5; $i++)
                    <span class="star-btn" data-value="{{ $i }}">★</span>
                @endfor
            </div>
        </div>

        @foreach([['quality_rating','🎨 جودة العمل'],['punctuality_rating','⏰ الالتزام'],['behavior_rating','🤝 التعامل']] as $c)
            <div class="text-right">
                <p class="font-bold text-slate-700 text-sm mb-2">{{ $c[1] }}</p>
                <div class="flex gap-1 stars" data-criteria="{{ $c[0] }}" dir="ltr">
                    @for($i = 1; $i <= 5; $i++)
                        <span class="star-btn" data-value="{{ $i }}" style="font-size:1.5rem;">★</span>
                    @endfor
                </div>
                <input type="hidden" name="{{ $c[0] }}" value="0">
            </div>
        @endforeach

        <div class="text-right">
            <label class="font-bold text-slate-700 text-sm block mb-2">تعليق (اختياري)</label>
            <textarea name="comment" rows="3" class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200 focus:border-gold-400 outline-none font-cairo text-sm"></textarea>
        </div>

        <button type="submit" class="w-full py-4 rounded-2xl text-white font-black"
                style="background: linear-gradient(135deg, #D4A24C, #B8873A);">
            <i class="fa-solid fa-paper-plane"></i> إرسال التقييم
        </button>
    </form>

    <a href="{{ route('client.dashboard') }}" class="inline-block mt-4 text-slate-500 text-sm hover:text-gold-600">
        تخطي التقييم
    </a>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('#mainStars .star-btn').forEach(s => s.addEventListener('click', () => {
    const v = parseInt(s.dataset.value);
    document.getElementById('ratingInput').value = v;
    document.querySelectorAll('#mainStars .star-btn').forEach(x => x.classList.toggle('active', parseInt(x.dataset.value) <= v));
}));

document.querySelectorAll('.stars[data-criteria]').forEach(container => {
    const input = container.parentElement.querySelector('input[type=hidden]');
    container.querySelectorAll('.star-btn').forEach(s => s.addEventListener('click', () => {
        const v = parseInt(s.dataset.value);
        input.value = v;
        container.querySelectorAll('.star-btn').forEach(x => x.classList.toggle('active', parseInt(x.dataset.value) <= v));
    }));
});
</script>
@endpush
