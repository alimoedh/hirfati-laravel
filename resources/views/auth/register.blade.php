@extends('layouts.auth')
@section('title', 'إنشاء حساب جديد')

@push('styles')
<style>
    .glass-card {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.08) 0%, rgba(255, 255, 255, 0.02) 100%);
        backdrop-filter: blur(40px) saturate(180%);
        -webkit-backdrop-filter: blur(40px) saturate(180%);
        border: 1px solid rgba(255, 255, 255, 0.12);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4), inset 0 1px 0 rgba(255, 255, 255, 0.1);
    }

    .glass-input {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .glass-input:focus-within {
        background: rgba(255, 255, 255, 0.08);
        border-color: rgba(212, 162, 76, 0.5);
        box-shadow: 0 0 0 4px rgba(212, 162, 76, 0.15);
    }

    .role-card {
        background: rgba(255, 255, 255, 0.03);
        border: 2px solid rgba(255, 255, 255, 0.08);
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .role-card:hover {
        background: rgba(255, 255, 255, 0.06);
        border-color: rgba(212, 162, 76, 0.3);
    }

    .role-card.active {
        background: linear-gradient(135deg, rgba(212, 162, 76, 0.15), rgba(212, 162, 76, 0.05));
        border-color: #D4A24C;
        box-shadow: 0 0 30px rgba(212, 162, 76, 0.3);
    }

    .role-card.active .role-icon {
        color: #D4A24C;
        transform: scale(1.1);
    }

    .role-icon {
        transition: all 0.3s ease;
    }

    .btn-gradient {
        background: linear-gradient(135deg, #0F1E3C 0%, #1E3A5F 50%, #2D4E80 100%);
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(212, 162, 76, 0.3);
        transition: all 0.3s ease;
    }

    .btn-gradient::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, transparent, rgba(212, 162, 76, 0.3), transparent);
        opacity: 0;
        transition: opacity 0.4s;
    }

    .btn-gradient:hover::before { opacity: 1; }
    .btn-gradient:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 40px rgba(212, 162, 76, 0.4);
    }

    /* File upload */
    .file-upload {
        border: 2px dashed rgba(255, 255, 255, 0.15);
        background: rgba(255, 255, 255, 0.03);
        transition: all 0.3s ease;
    }
    .file-upload:hover {
        border-color: rgba(212, 162, 76, 0.5);
        background: rgba(255, 255, 255, 0.05);
    }

    /* Progress bar للـ steps */
    .step-indicator {
        background: rgba(255, 255, 255, 0.08);
        height: 2px;
        flex: 1;
        position: relative;
        overflow: hidden;
    }
    .step-indicator.active::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(90deg, #D4A24C, #E5B968);
        animation: fillBar 0.5s ease-out;
    }
    @keyframes fillBar { from { width: 0; } to { width: 100%; } }
</style>
@endpush

@section('content')

<div class="min-h-screen flex items-center justify-center p-4 py-10 relative">

    {{-- Particles Background --}}
    <div class="fixed inset-0 -z-10 pointer-events-none">
        @for($i = 0; $i < 15; $i++)
            <div class="absolute rounded-full bg-gold-400/60"
                 style="width: {{ rand(2, 5) }}px;
                        height: {{ rand(2, 5) }}px;
                        left: {{ rand(0, 100) }}%;
                        top: {{ rand(0, 100) }}%;
                        box-shadow: 0 0 12px rgba(212, 162, 76, 0.9);
                        animation: float {{ rand(6, 12) }}s ease-in-out infinite;
                        animation-delay: {{ rand(0, 3) }}s;"></div>
        @endfor
    </div>

    <div class="w-full max-w-2xl">

        {{-- Logo --}}
        <div class="text-center mb-8">
            <img src="{{ asset('assets/images/logo.png') }}"
                 alt="{{ config('hirfati.site_name') }}"
                 class="w-36 mx-auto mb-4">
            <h1 class="text-3xl lg:text-4xl font-black text-white mb-2">
                انضم إلينا اليوم
            </h1>
            <p class="text-white/60">
                أنشئ حسابك مجاناً وابدأ الآن
            </p>
        </div>

        {{-- Card --}}
        <div class="glass-card rounded-3xl p-8 animate-fade-up">

            {{-- Errors --}}
            @if($errors->any())
                <div class="mb-6 bg-red-500/20 border border-red-500/30 rounded-2xl p-4 flex items-start gap-3">
                    <i class="fa-solid fa-circle-exclamation text-red-400 mt-0.5"></i>
                    <div class="text-red-200 text-sm">
                        @foreach($errors->all() as $error)
                            <p>• {{ $error }}</p>
                        @endforeach
                    </div>
                </div>
            @endif

            <form action="{{ route('auth.register') }}" method="POST" enctype="multipart/form-data" x-data="{ role: '{{ old('role', 'client') }}' }">
                @csrf

                {{-- Role Selector --}}
                <div class="mb-6">
                    <p class="text-white/80 text-sm font-bold mb-3">اختر نوع الحساب:</p>
                    <div class="grid grid-cols-2 gap-4">
                        {{-- Client --}}
                        <div @click="role = 'client'"
                             :class="role === 'client' ? 'active' : ''"
                             class="role-card rounded-2xl p-5 text-center">
                            <i class="fa-solid fa-user role-icon text-3xl text-white/40 mb-2 block"></i>
                            <p class="text-white font-bold text-sm">عميل</p>
                            <p class="text-white/40 text-xs mt-1">طالب خدمة</p>
                        </div>
                        {{-- Craftsman --}}
                        <div @click="role = 'craftsman'"
                             :class="role === 'craftsman' ? 'active' : ''"
                             class="role-card rounded-2xl p-5 text-center">
                            <i class="fa-solid fa-user-gear role-icon text-3xl text-white/40 mb-2 block"></i>
                            <p class="text-white font-bold text-sm">حرفي</p>
                            <p class="text-white/40 text-xs mt-1">مقدم خدمة</p>
                        </div>
                    </div>
                    <input type="hidden" name="role" :value="role">
                </div>

                {{-- Basic Fields --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

                    {{-- Full Name --}}
                    <div class="lg:col-span-2">
                        <label class="block text-white/80 text-sm font-bold mb-2">الاسم الكامل</label>
                        <div class="glass-input rounded-2xl flex items-center px-4 py-3.5">
                            <i class="fa-solid fa-id-card text-gold-400 ml-3"></i>
                            <input type="text" name="full_name" value="{{ old('full_name') }}"
                                   placeholder="أدخل اسمك الثلاثي" required
                                   class="bg-transparent border-0 outline-none flex-1 text-white placeholder-white/40 font-cairo text-sm focus:ring-0 p-0">
                        </div>
                    </div>

                    {{-- Phone --}}
                    <div>
                        <label class="block text-white/80 text-sm font-bold mb-2">رقم الهاتف</label>
                        <div class="glass-input rounded-2xl flex items-center px-4 py-3.5">
                            <i class="fa-solid fa-phone text-gold-400 ml-3"></i>
                            <input type="tel" name="phone" value="{{ old('phone') }}"
                                   placeholder="07XXXXXXXX" required
                                   class="bg-transparent border-0 outline-none flex-1 text-white placeholder-white/40 font-cairo text-sm focus:ring-0 p-0">
                        </div>
                    </div>

                    {{-- Email --}}
                    <div>
                        <label class="block text-white/80 text-sm font-bold mb-2">البريد الإلكتروني</label>
                        <div class="glass-input rounded-2xl flex items-center px-4 py-3.5">
                            <i class="fa-solid fa-envelope text-gold-400 ml-3"></i>
                            <input type="email" name="email" value="{{ old('email') }}"
                                   placeholder="example@email.com" required
                                   class="bg-transparent border-0 outline-none flex-1 text-white placeholder-white/40 font-cairo text-sm focus:ring-0 p-0">
                        </div>
                    </div>

                    {{-- Password --}}
                    <div class="lg:col-span-2" x-data="{ show: false }">
                        <label class="block text-white/80 text-sm font-bold mb-2">كلمة المرور</label>
                        <div class="glass-input rounded-2xl flex items-center px-4 py-3.5">
                            <i class="fa-solid fa-lock text-gold-400 ml-3"></i>
                            <input :type="show ? 'text' : 'password'" name="password"
                                   placeholder="6 أحرف على الأقل" required minlength="6"
                                   class="bg-transparent border-0 outline-none flex-1 text-white placeholder-white/40 font-cairo text-sm focus:ring-0 p-0">
                            <button type="button" @click="show = !show"
                                    class="text-white/40 hover:text-gold-400">
                                <i class="fa-solid" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Craftsman Fields --}}
                <div x-show="role === 'craftsman'"
                     x-transition
                     class="mt-6 p-5 rounded-2xl bg-white/[0.03] border border-white/[0.08] space-y-4">

                    <p class="text-gold-400 text-sm font-bold flex items-center gap-2">
                        <i class="fa-solid fa-user-gear"></i>
                        بيانات الحرفي
                    </p>

                    {{-- Category --}}
                    <div>
                        <label class="block text-white/80 text-sm font-bold mb-2">التخصص *</label>
                        <div class="glass-input rounded-2xl flex items-center px-4 py-3.5">
                            <i class="fa-solid fa-wrench text-gold-400 ml-3"></i>
                            <select name="category_id" :required="role === 'craftsman'"
                                    class="bg-transparent border-0 outline-none flex-1 text-white font-cairo text-sm focus:ring-0 p-0 appearance-none cursor-pointer">
                                <option value="" class="bg-brand-800">-- اختر تخصصك --</option>
                                @foreach(\App\Models\Category::active()->get() as $cat)
                                    <option value="{{ $cat->id }}" class="bg-brand-800" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Identity Document --}}
                    <div>
                        <label class="block text-white/80 text-sm font-bold mb-2">صورة الهوية *</label>
                        <label class="file-upload rounded-2xl p-6 flex flex-col items-center justify-center cursor-pointer">
                            <i class="fa-solid fa-cloud-arrow-up text-3xl text-gold-400 mb-2"></i>
                            <p class="text-white font-semibold text-sm">اضغط لرفع صورة الهوية</p>
                            <p class="text-white/40 text-xs mt-1">(JPG, PNG - بحد أقصى 5MB)</p>
                            <input type="file" name="identity_document" accept="image/*"
                                   :required="role === 'craftsman'"
                                   class="hidden"
                                   onchange="document.getElementById('file-name').textContent = '✅ ' + this.files[0].name">
                        </label>
                        <p id="file-name" class="text-emerald-400 text-xs mt-2 text-center"></p>
                    </div>

                    <div class="flex items-start gap-2 bg-amber-500/10 border border-amber-500/20 rounded-xl p-3">
                        <i class="fa-solid fa-info-circle text-amber-400 mt-0.5"></i>
                        <p class="text-amber-200 text-xs leading-relaxed">
                            سيتم مراجعة طلبك من قبل الإدارة قبل تفعيل حسابك كحرفي.
                        </p>
                    </div>
                </div>

                {{-- Submit --}}
                <button type="submit"
                        class="btn-gradient w-full mt-8 py-4 rounded-2xl text-white font-black text-base flex items-center justify-center gap-3 relative z-10">
                    <i class="fa-solid fa-user-plus"></i>
                    <span>إنشاء الحساب</span>
                </button>

                {{-- Footer --}}
                <div class="mt-6 text-center">
                    <p class="text-white/60 text-sm">
                        لديك حساب بالفعل؟
                        <a href="{{ route('auth.login') }}"
                           class="text-gold-400 hover:text-gold-300 font-bold mr-1">
                            تسجيل الدخول
                        </a>
                    </p>
                </div>
            </form>
        </div>

        <p class="text-center text-white/30 text-xs mt-6">
            © {{ date('Y') }} {{ config('hirfati.site_name') }} — جميع الحقوق محفوظة
        </p>
    </div>
</div>

@endsection
