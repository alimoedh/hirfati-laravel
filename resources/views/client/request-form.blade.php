@extends('layouts.client')
@section('title', 'طلب خدمة جديدة')

@push('styles')
<style>
    .form-label { font-weight: 800; color: #334155; font-size: 0.8rem; margin-bottom: 0.4rem; display: block; }
    .form-input { width: 100%; padding: 0.85rem 1.25rem; border: 2px solid #E2E8F0; border-radius: 16px; font-size: 0.9rem; font-family: 'Cairo', sans-serif; outline: none; transition: all 0.3s; background: white; }
    .form-input:focus { border-color: #D4A24C; box-shadow: 0 0 0 4px rgba(212,162,76,0.1); }
    .section-box { padding: 1.25rem; border-radius: 20px; border: 2px solid; }
</style>
@endpush

@section('content')
<div class="mb-6">
    <a href="{{ route('client.dashboard') }}" class="inline-flex items-center gap-2 text-slate-500 hover:text-gold-600 text-sm font-bold mb-3">
        <i class="fa-solid fa-arrow-right"></i> العودة
    </a>
    <h1 class="text-2xl lg:text-3xl font-black text-slate-800">
        <i class="fa-solid fa-plus-circle text-gold-500"></i>
        طلب خدمة جديدة
    </h1>
</div>

<div class="bg-white rounded-3xl p-6 lg:p-8 shadow-soft border border-slate-100 max-w-3xl mx-auto">

    @if($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 rounded-2xl p-4">
            <ul class="text-red-700 text-sm space-y-1">
                @foreach($errors->all() as $e) <li>• {{ $e }}</li> @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('client.request-form.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf

        <div>
            <label class="form-label">📌 نوع الخدمة *</label>
            <select name="category_id" id="category_id" required class="form-input">
                <option value="">-- اختر التخصص --</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="form-label">👤 اختر الحرفي (اختياري)</label>
            <select name="craftsman_id" id="craftsman_id" class="form-input">
                <option value="">-- اختر الحرفي (اختياري) --</option>
            </select>
            <p class="text-xs text-slate-400 mt-1">إذا لم تختر حرفياً، سيظهر الطلب لجميع الحرفيين في هذا التخصص</p>
        </div>

        <div>
            <label class="form-label">📝 عنوان الخدمة *</label>
            <input type="text" name="title" value="{{ old('title') }}" placeholder="مثال: تركيب مكيف..." required class="form-input">
        </div>

        <div>
            <label class="form-label">📄 وصف المشكلة *</label>
            <textarea name="description" rows="4" placeholder="اشرح المشكلة بالتفصيل..." required class="form-input">{{ old('description') }}</textarea>
        </div>

        <div>
            <label class="form-label">📍 العنوان *</label>
            <input type="text" name="address" value="{{ old('address') }}" placeholder="صنعاء - شارع..." required class="form-input">
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="form-label">📅 التاريخ *</label>
                <input type="date" name="preferred_date" value="{{ old('preferred_date', now()->addDay()->format('Y-m-d')) }}" required class="form-input">
            </div>
            <div>
                <label class="form-label">⏰ الوقت</label>
                <input type="time" name="preferred_time" value="{{ old('preferred_time') }}" class="form-input">
            </div>
        </div>

        {{-- AI Estimate Box --}}
        <div class="section-box" style="background: #EFF6FF; border-color: #BFDBFE;">
            <div class="flex items-center justify-between flex-wrap gap-2 mb-3">
                <label class="form-label" style="margin:0;">💰 الميزانية (ريال) - اختياري</label>
                <button type="button" onclick="getAIEstimate()" id="aiEstimateBtn"
                        class="px-4 py-2 rounded-xl text-white font-bold text-xs transition-all hover:shadow-glow"
                        style="background: linear-gradient(135deg, #8B5CF6, #6D28D9);">
                    <i class="fa-solid fa-robot"></i> احسب التقدير الذكي
                </button>
            </div>
            <input type="number" name="budget" id="budgetInput" value="{{ old('budget') }}" placeholder="5000" min="0" step="100" class="form-input">

            <div id="aiEstimateResult" class="hidden mt-3 p-4 bg-white rounded-2xl border-2 border-purple-300">
                <div class="flex items-center gap-2 mb-2">
                    <i class="fa-solid fa-robot text-purple-600"></i>
                    <strong class="text-purple-700 text-sm">تقدير الذكاء الاصطناعي</strong>
                </div>
                <div id="aiEstimateContent"></div>
            </div>
        </div>

        <div>
            <label class="form-label">🖼️ صورة المشكلة (اختياري)</label>
            <input type="file" name="problem_image" accept="image/*" class="form-input" style="padding: 0.5rem;">
        </div>

        {{-- Emergency --}}
        <div class="section-box" style="background: #FEF2F2; border-color: #FECACA;">
            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" name="is_emergency" value="1" {{ old('is_emergency') ? 'checked' : '' }} class="w-5 h-5">
                <span class="font-bold text-red-700 text-sm">
                    <i class="fa-solid fa-bolt"></i> طلب طوارئ (خدمة خلال ساعة) — رسوم إضافية 2,000 ريال
                </span>
            </label>
        </div>

        {{-- Installment --}}
        <div class="section-box" style="background: #EFF6FF; border-color: #BFDBFE;" x-data="{ on: false }">
            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" name="use_installment" value="1" x-model="on" class="w-5 h-5">
                <span class="font-bold text-blue-700 text-sm">
                    <i class="fa-solid fa-coins"></i> الدفع بالتقسيط
                </span>
            </label>
            <div x-show="on" class="mt-3">
                <label class="form-label">عدد الأقساط</label>
                <select name="installment_count" class="form-input">
                    <option value="3">3 أقساط</option>
                    <option value="6">6 أقساط</option>
                    <option value="12">12 قسط</option>
                </select>
            </div>
        </div>

        <button type="submit" class="w-full py-4 rounded-2xl text-white font-black text-base transition-all hover:-translate-y-1 hover:shadow-glow"
                style="background: linear-gradient(135deg, #D4A24C, #B8873A);">
            <i class="fa-solid fa-paper-plane ml-2"></i>
            تأكيد وإرسال الطلب
        </button>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('category_id').addEventListener('change', function() {
    const id = this.value;
    const select = document.getElementById('craftsman_id');
    if (!id) { select.innerHTML = '<option value="">-- اختر الحرفي --</option>'; return; }
    select.innerHTML = '<option value="">جاري التحميل...</option>';
    fetch(`{{ route('client.api.craftsmen-by-category') }}?category_id=${id}`)
        .then(r => r.json())
        .then(data => {
            select.innerHTML = '<option value="">-- اختر الحرفي (اختياري) --</option>';
            data.forEach(c => select.innerHTML += `<option value="${c.id}">${c.full_name}</option>`);
        });
});

function getAIEstimate() {
    const catId = document.getElementById('category_id').value;
    const desc = document.querySelector('textarea[name="description"]').value;
    const emerg = document.querySelector('input[name="is_emergency"]')?.checked ? 1 : 0;
    const btn = document.getElementById('aiEstimateBtn');

    if (!catId) { alert('اختر نوع الخدمة أولاً'); return; }
    if (desc.length < 10) { alert('اكتب وصف المشكلة'); return; }

    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> جاري الحساب...';

    const fd = new FormData();
    fd.append('category_id', catId);
    fd.append('description', desc);
    if (emerg) fd.append('is_emergency', 1);

    fetch('{{ route('client.request-form.ai-estimate') }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        body: fd
    })
    .then(r => r.json())
    .then(res => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-robot"></i> احسب التقدير';
        if (res.success) {
            const d = res.data;
            document.getElementById('aiEstimateContent').innerHTML = `
                <div class="text-center mb-2">
                    <div class="text-xs text-slate-500">السعر المقترح</div>
                    <div class="text-3xl font-black text-emerald-600">${d.estimate.toLocaleString('ar-EG')} <span class="text-sm">ريال</span></div>
                </div>
                <button type="button" onclick="document.getElementById('budgetInput').value=${d.estimate}; document.getElementById('aiEstimateResult').classList.add('hidden');"
                        class="w-full py-2 rounded-xl text-white font-bold text-sm" style="background: linear-gradient(135deg, #10B981, #059669);">
                    <i class="fa-solid fa-check"></i> استخدم هذا التقدير
                </button>
            `;
            document.getElementById('aiEstimateResult').classList.remove('hidden');
        }
    })
    .catch(() => { btn.disabled = false; btn.innerHTML = '<i class="fa-solid fa-robot"></i> احسب التقدير'; });
}
</script>
@endpush
