@extends('layouts.admin')
@section('title', 'الإعدادات')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl lg:text-3xl font-black text-slate-800">
        <i class="fa-solid fa-gear text-gold-500"></i> الإعدادات
    </h1>
</div>

<form method="POST" action="{{ route('admin.settings.update') }}">
    @csrf
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

        <div class="bg-white rounded-3xl p-6 shadow-soft border border-slate-100">
            <h3 class="font-black text-slate-800 mb-4 flex items-center gap-2">
                <i class="fa-solid fa-globe text-blue-500"></i> عام
            </h3>
            <div class="space-y-4">
                @foreach([
                    ['site_name','اسم الموقع','text','حرفتي'],
                    ['site_description','الوصف','text','منصة تجمع الحرفيين والعملاء'],
                    ['contact_email','البريد','email','info@hirfati.com'],
                    ['contact_phone','الهاتف','tel','0770000000'],
                ] as $f)
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1">{{ $f[1] }}</label>
                        <input type="{{ $f[2] }}" name="settings[{{ $f[0] }}]"
                               value="{{ $settings[$f[0]] ?? $f[3] }}"
                               class="w-full px-4 py-2.5 rounded-xl border-2 border-slate-200 focus:border-gold-400 outline-none font-cairo text-sm">
                    </div>
                @endforeach
            </div>
        </div>

        <div class="bg-white rounded-3xl p-6 shadow-soft border border-slate-100">
            <h3 class="font-black text-slate-800 mb-4 flex items-center gap-2">
                <i class="fa-solid fa-percent text-amber-500"></i> العمولات
            </h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">نسبة العمولة (%)</label>
                    <input type="number" name="settings[commission_percentage]" value="{{ $settings['commission_percentage'] ?? 5 }}" min="0" max="100"
                           class="w-full px-4 py-2.5 rounded-xl border-2 border-slate-200 focus:border-gold-400 outline-none font-cairo text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">الحد الأدنى للسحب (ريال)</label>
                    <input type="number" name="settings[min_withdrawal]" value="{{ $settings['min_withdrawal'] ?? 1000 }}" min="0"
                           class="w-full px-4 py-2.5 rounded-xl border-2 border-slate-200 focus:border-gold-400 outline-none font-cairo text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">مدة الضمان (أيام)</label>
                    <input type="number" name="settings[warranty_days]" value="{{ $settings['warranty_days'] ?? 30 }}" min="0"
                           class="w-full px-4 py-2.5 rounded-xl border-2 border-slate-200 focus:border-gold-400 outline-none font-cairo text-sm">
                </div>
            </div>
        </div>

        <div class="bg-white rounded-3xl p-6 shadow-soft border border-slate-100 lg:col-span-2">
            <h3 class="font-black text-slate-800 mb-4 flex items-center gap-2">
                <i class="fa-solid fa-bolt text-yellow-500"></i> خدمات إضافية
            </h3>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                @foreach([
                    ['emergency_enabled','تفعيل الطوارئ'],
                    ['installment_enabled','تفعيل التقسيط'],
                ] as $f)
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1">{{ $f[1] }}</label>
                        <select name="settings[{{ $f[0] }}]" class="w-full px-4 py-2.5 rounded-xl border-2 border-slate-200 focus:border-gold-400 outline-none font-cairo text-sm">
                            <option value="true" {{ ($settings[$f[0]] ?? 'true') === 'true' ? 'selected' : '' }}>مفعل</option>
                            <option value="false" {{ ($settings[$f[0]] ?? 'true') === 'false' ? 'selected' : '' }}>معطل</option>
                        </select>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <button type="submit" class="mt-5 px-8 py-4 rounded-2xl text-white font-black"
            style="background: linear-gradient(135deg, #D4A24C, #B8873A);">
        <i class="fa-solid fa-save"></i> حفظ الإعدادات
    </button>
</form>
@endsection
