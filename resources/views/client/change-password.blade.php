@extends('layouts.client')
@section('title', 'تغيير كلمة المرور')

@section('content')
<div class="mb-6">
    <a href="{{ route('client.profile') }}" class="inline-flex items-center gap-2 text-slate-500 hover:text-gold-600 text-sm font-bold mb-3">
        <i class="fa-solid fa-arrow-right"></i> العودة
    </a>
    <h1 class="text-2xl lg:text-3xl font-black text-slate-800">
        <i class="fa-solid fa-key text-gold-500"></i> تغيير كلمة المرور
    </h1>
</div>

<div class="bg-white rounded-3xl p-6 lg:p-8 shadow-soft border border-slate-100 max-w-md mx-auto">
    <form method="POST" action="{{ route('client.change-password.update') }}" class="space-y-4">
        @csrf
        @foreach([['current_password','كلمة المرور الحالية'],['new_password','كلمة المرور الجديدة'],['confirm_password','تأكيد كلمة المرور']] as $f)
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-2">{{ $f[1] }}</label>
                <input type="password" name="{{ $f[0] }}" required
                       class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200 focus:border-gold-400 outline-none font-cairo text-sm">
            </div>
        @endforeach
        <button type="submit" class="w-full py-3 rounded-2xl text-white font-bold text-sm transition-all hover:shadow-glow"
                style="background: linear-gradient(135deg, #D4A24C, #B8873A);">
            <i class="fa-solid fa-save"></i> تغيير كلمة المرور
        </button>
    </form>
</div>
@endsection
