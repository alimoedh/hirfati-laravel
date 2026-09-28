@extends('layouts.client')
@section('title', 'إتمام الدفع')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl lg:text-3xl font-black text-slate-800">
        <i class="fa-solid fa-credit-card text-gold-500"></i> إتمام الدفع
    </h1>
</div>

<div class="bg-amber-50 border-r-4 border-amber-400 rounded-2xl p-4 mb-5 text-sm text-amber-800">
    <i class="fa-solid fa-info-circle"></i> <strong>بيئة تجريبية:</strong> هذا دفع تجريبي فقط.
</div>

<form method="POST" action="{{ route('client.checkout.process', $request->id) }}">
    @csrf
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        {{-- Payment Methods --}}
        <div class="lg:col-span-2 bg-white rounded-3xl p-6 shadow-soft border border-slate-100">
            <h3 class="font-black text-slate-800 mb-4">اختر طريقة الدفع</h3>

            @php
                $methods = [
                    ['card', 'fa-credit-card', 'بطاقة ائتمانية', 'Visa · MasterCard'],
                    ['paypal', 'fa-paypal', 'PayPal', 'الدفع عبر PayPal'],
                    ['wallet', 'fa-mobile-screen', 'محفظة إلكترونية', 'جوالي · كاش'],
                ];
            @endphp

            <div class="space-y-3">
                @foreach($methods as $i => $m)
                    <label class="flex items-center gap-4 p-4 rounded-2xl border-2 border-slate-200 hover:border-gold-400 cursor-pointer transition-all has-[:checked]:border-gold-500 has-[:checked]:bg-amber-50">
                        <input type="radio" name="payment_method" value="{{ $m[0] }}" {{ $i === 0 ? 'checked' : '' }} class="w-4 h-4">
                        <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center text-xl">
                            <i class="fa-solid {{ $m[1] }}"></i>
                        </div>
                        <div>
                            <p class="font-bold text-slate-800 text-sm">{{ $m[2] }}</p>
                            <p class="text-xs text-slate-500">{{ $m[3] }}</p>
                        </div>
                    </label>
                @endforeach
            </div>
        </div>

        {{-- Summary --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-3xl p-6 shadow-soft border border-slate-100 sticky top-24">
                <h3 class="font-black text-slate-800 mb-4">ملخص الطلب</h3>
                <div class="space-y-3 text-sm border-b border-slate-100 pb-4 mb-4">
                    <div class="flex justify-between"><span class="text-slate-500">الطلب</span><span class="font-bold">#{{ str_pad($request->id, 4, '0', STR_PAD_LEFT) }}</span></div>
                    <div class="flex justify-between"><span class="text-slate-500">الخدمة</span><span class="font-bold truncate max-w-[150px]">{{ $request->title }}</span></div>
                    <div class="flex justify-between"><span class="text-slate-500">التصنيف</span><span class="font-bold">{{ $request->category->name ?? '-' }}</span></div>
                </div>
                <div class="flex justify-between items-center mb-4">
                    <span class="font-black text-slate-800">الإجمالي</span>
                    <span class="text-2xl font-black text-gold-600">{{ number_format($request->budget ?? 0) }} <span class="text-sm">ر.ي</span></span>
                </div>
                <button type="submit" class="w-full py-4 rounded-2xl text-white font-black text-sm"
                        style="background: linear-gradient(135deg, #10B981, #059669);">
                    <i class="fa-solid fa-lock"></i> تأكيد الدفع
                </button>
                <a href="{{ route('client.request-details', $request->id) }}" class="block text-center mt-3 text-xs text-slate-500 hover:text-gold-600">
                    <i class="fa-solid fa-xmark"></i> إلغاء
                </a>
            </div>
        </div>
    </div>
</form>
@endsection
