@extends('layouts.client')
@section('title', 'تقديم شكوى')

@section('content')
<div class="mb-6">
    <a href="{{ $backUrl }}" class="inline-flex items-center gap-2 text-slate-500 hover:text-gold-600 text-sm font-bold mb-3">
        <i class="fa-solid fa-arrow-right"></i> العودة
    </a>
    <h1 class="text-2xl lg:text-3xl font-black text-red-600">
        <i class="fa-solid fa-triangle-exclamation"></i> تقديم شكوى
    </h1>
</div>

<div class="bg-white rounded-3xl p-6 lg:p-8 shadow-soft border border-slate-100 max-w-2xl mx-auto">
    <p class="text-center text-slate-600 mb-6">
        شكوى ضد: <strong class="text-slate-800">{{ $targetName }}</strong>
    </p>

    <form method="POST" action="{{ route('client.complaint.store') }}" enctype="multipart/form-data" class="space-y-4">
        @csrf
        <input type="hidden" name="request_id" value="{{ $serviceRequest->id }}">
        <input type="hidden" name="is_craftsman" value="{{ $isCraftsman ? 1 : 0 }}">

        <div>
            <label class="block text-xs font-bold text-slate-600 mb-2">رقم الطلب</label>
            <input type="text" value="#{{ str_pad($serviceRequest->id, 4, '0', STR_PAD_LEFT) }}" readonly
                   class="w-full px-4 py-3 rounded-2xl bg-slate-50 border-2 border-slate-100 font-bold">
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-600 mb-2">سبب الشكوى *</label>
            <select name="reason" required class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200 focus:border-gold-400 outline-none font-cairo text-sm">
                <option value="">اختر السبب...</option>
                <option value="quality">جودة الخدمة</option>
                <option value="delay">تأخر الموعد</option>
                <option value="price">خلاف على السعر</option>
                <option value="behavior">سلوك غير لائق</option>
                <option value="other">سبب آخر</option>
            </select>
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-600 mb-2">التفاصيل *</label>
            <textarea name="details" rows="5" required placeholder="اكتب التفاصيل..."
                      class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200 focus:border-gold-400 outline-none font-cairo text-sm"></textarea>
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-600 mb-2">أدلة مصورة (اختياري)</label>
            <input type="file" name="evidence_image" accept="image/*"
                   class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200">
        </div>

        <button type="submit" class="w-full py-4 rounded-2xl bg-red-500 hover:bg-red-600 text-white font-black">
            <i class="fa-solid fa-paper-plane"></i> إرسال الشكوى
        </button>
    </form>
</div>
@endsection
