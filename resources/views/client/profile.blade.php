@extends('layouts.client')
@section('title', 'الملف الشخصي')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl lg:text-3xl font-black text-slate-800">
        <i class="fa-solid fa-user text-gold-500"></i> الملف الشخصي
    </h1>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Info Card --}}
    <div class="lg:col-span-1">
        <div class="bg-white rounded-3xl p-6 shadow-soft border border-slate-100 text-center">
            <img src="{{ $user->avatar_url }}" class="w-24 h-24 rounded-full border-4 border-gold-400 object-cover mx-auto mb-4">
            <h2 class="text-xl font-black text-slate-800">{{ $user->full_name }}</h2>
            @if($user->is_verified)
                <span class="inline-flex items-center gap-1 bg-emerald-100 text-emerald-700 px-3 py-1 rounded-xl text-xs font-bold mt-2">
                    <i class="fa-solid fa-circle-check"></i> معتمد
                </span>
            @endif
            <div class="bg-gradient-to-br from-brand-800 to-brand-600 rounded-2xl p-4 mt-4 text-white">
                <p class="text-xs opacity-70">نقاط الولاء</p>
                <p class="text-3xl font-black text-gold-400">{{ $user->loyalty_points }}</p>
            </div>
        </div>
    </div>

    {{-- Edit Form --}}
    <div class="lg:col-span-2">
        <div class="bg-white rounded-3xl p-6 shadow-soft border border-slate-100">
            <h3 class="font-black text-slate-800 mb-5 flex items-center gap-2">
                <i class="fa-solid fa-pen text-gold-500"></i> تعديل البيانات
            </h3>

            <form method="POST" action="{{ route('client.profile.update') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-2">الاسم الكامل</label>
                    <input type="text" name="full_name" value="{{ old('full_name', $user->full_name) }}" required
                           class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200 focus:border-gold-400 outline-none font-cairo text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-2">البريد الإلكتروني</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                           class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200 focus:border-gold-400 outline-none font-cairo text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-2">رقم الهاتف</label>
                    <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}" required
                           class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200 focus:border-gold-400 outline-none font-cairo text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-2">الصورة الشخصية</label>
                    <input type="file" name="avatar" accept="image/*"
                           class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200 focus:border-gold-400 outline-none font-cairo text-sm">
                </div>

                <div class="flex gap-3 pt-3">
                    <button type="submit" class="flex-1 py-3 rounded-2xl text-white font-bold text-sm transition-all hover:shadow-glow"
                            style="background: linear-gradient(135deg, #D4A24C, #B8873A);">
                        <i class="fa-solid fa-save"></i> حفظ التغييرات
                    </button>
                    <a href="{{ route('client.dashboard') }}" class="px-6 py-3 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-sm">
                        إلغاء
                    </a>
                </div>
            </form>

            <div class="border-t border-slate-100 mt-6 pt-5">
                <h4 class="font-black text-slate-800 mb-3 text-sm">الأمان</h4>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('client.change-password') }}" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs flex items-center gap-2">
                        <i class="fa-solid fa-key"></i> تغيير كلمة المرور
                    </a>
                    <a href="{{ route('auth.verify') }}" class="px-4 py-2 rounded-xl bg-purple-100 hover:bg-purple-200 text-purple-700 font-bold text-xs flex items-center gap-2">
                        <i class="fa-solid fa-shield-halved"></i> التحقق من الهوية
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
