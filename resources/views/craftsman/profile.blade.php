@extends('layouts.craftsman')
@section('title', 'الملف الشخصي')

@section('content')
<div class="mb-6 flex items-center justify-between flex-wrap gap-3">
    <h1 class="text-2xl lg:text-3xl font-black text-slate-800">
        <i class="fa-solid fa-user-gear text-gold-500"></i> الملف الشخصي
    </h1>
    <div class="flex gap-2 flex-wrap">
        <a href="{{ auth()->user()->publicProfileUrl() }}" target="_blank"
           class="px-4 py-2 rounded-xl bg-gold-100 hover:bg-gold-200 text-gold-700 font-bold text-sm">
            <i class="fa-solid fa-external-link"></i> عرض ملفك العام
        </a>
        <a href="{{ route('auth.verify') }}" class="px-4 py-2 rounded-xl bg-purple-100 hover:bg-purple-200 text-purple-700 font-bold text-sm">
            <i class="fa-solid fa-shield-halved"></i> التحقق من الهوية
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
    <div class="lg:col-span-1">
        <div class="bg-white rounded-3xl p-6 shadow-soft border border-slate-100 text-center">
            <img id="profileAvatar" src="{{ $user->avatar_url }}" class="w-24 h-24 rounded-full border-4 border-gold-400 object-cover mx-auto mb-4">
            <h2 class="text-xl font-black text-slate-800">{{ $user->full_name }}</h2>
            <p class="text-sm text-slate-500 mt-1">{{ $profile->category->name ?? 'غير محدد' }}</p>
            <p class="text-xs text-amber-600 mt-1">⭐ {{ number_format($profile->rating_avg ?? 0, 1) }} · {{ $profile->total_reviews ?? 0 }} تقييم</p>
        </div>
    </div>

    <div class="lg:col-span-2">
        <form method="POST" action="{{ route('craftsman.profile.update') }}" enctype="multipart/form-data" class="bg-white rounded-3xl p-6 shadow-soft border border-slate-100 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-2">الصورة الشخصية</label>
                <input type="file" name="avatar" accept="image/*" onchange="previewImage(this)" class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-2">سنوات الخبرة</label>
                    <input type="number" name="experience_years" value="{{ old('experience_years', $profile->experience_years ?? 0) }}" min="0" max="50"
                           class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200 focus:border-gold-400 outline-none font-cairo text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-2">السعر/ساعة (ر.ي)</label>
                    <input type="number" name="hourly_rate" value="{{ old('hourly_rate', $profile->hourly_rate ?? 0) }}" min="0" step="10"
                           class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200 focus:border-gold-400 outline-none font-cairo text-sm">
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-2">نبذة عنك</label>
                <textarea name="bio" rows="3" class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200 focus:border-gold-400 outline-none font-cairo text-sm">{{ old('bio', $profile->bio ?? '') }}</textarea>
            </div>
            <div class="p-4 rounded-2xl bg-red-50 border-r-4 border-red-400">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_emergency" value="1" {{ $profile->is_emergency ? 'checked' : '' }} class="w-5 h-5">
                    <span class="font-bold text-red-700 text-sm"><i class="fa-solid fa-bolt"></i> تفعيل خدمة الطوارئ</span>
                </label>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-2">رقم الطوارئ</label>
                <input type="tel" name="emergency_phone" value="{{ old('emergency_phone', $profile->emergency_phone ?? '') }}"
                       class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200 focus:border-gold-400 outline-none font-cairo text-sm">
            </div>
            <button type="submit" class="w-full py-3 rounded-2xl text-white font-black"
                    style="background: linear-gradient(135deg, #D4A24C, #B8873A);">
                <i class="fa-solid fa-save"></i> حفظ التغييرات
            </button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function previewImage(input) {
    if (input.files?.[0]) {
        const reader = new FileReader();
        reader.onload = e => document.getElementById('profileAvatar').src = e.target.result;
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush
