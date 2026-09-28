@extends('layouts.admin')
@section('title', 'إدارة المستخدمين')

@section('content')
<div class="mb-6 flex items-center justify-between flex-wrap gap-4">
    <h1 class="text-2xl lg:text-3xl font-black text-slate-800">
        <i class="fa-solid fa-users text-gold-500"></i> إدارة المستخدمين
    </h1>,
    <div class="flex gap-2">
        <a href="{{ route('admin.reports.export-users') }}"
           class="px-5 py-3 rounded-2xl bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-sm">
            <i class="fa-solid fa-file-csv"></i> تصدير CSV
        </a>
        <a href="{{ route('auth.register') }}" target="_blank" class="px-5 py-3 rounded-2xl text-white font-bold text-sm"
           style="background: linear-gradient(135deg, #D4A24C, #B8873A);">
            <i class="fa-solid fa-plus"></i> إضافة مستخدم
        </a>
    </div>

</div>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-5">
    @foreach([
        ['total','إجمالي','fa-users','slate'],
        ['admins','إداري','fa-crown','blue'],
        ['craftsmen','حرفي','fa-user-gear','amber'],
        ['clients','عميل','fa-user','emerald'],
    ] as $s)
        <div class="bg-white rounded-2xl p-4 border border-slate-100 shadow-soft text-center">
            <i class="fa-solid {{ $s[2] }} text-{{ $s[3] }}-500 text-xl mb-2"></i>
            <p class="text-2xl font-black text-slate-800">{{ $stats[$s[0]] }}</p>
            <p class="text-xs text-slate-500 font-bold">{{ $s[1] }}</p>
        </div>
    @endforeach
</div>

<div class="bg-white rounded-3xl p-5 shadow-soft border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full" style="border-collapse: separate; border-spacing: 0;">
            <thead>
                <tr class="text-xs text-slate-400 uppercase border-b border-slate-100">
                    <th class="text-right py-3 px-2">#</th>
                    <th class="text-right py-3 px-2">الاسم</th>
                    <th class="text-right py-3 px-2">البريد</th>
                    <th class="text-right py-3 px-2">الدور</th>
                    <th class="text-right py-3 px-2">الحالة</th>
                    <th class="text-right py-3 px-2">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $u)
                    <tr class="hover:bg-slate-50 transition-colors border-b border-slate-50">
                        <td class="py-3 px-2 text-xs text-slate-500">{{ $u->id }}</td>
                        <td class="py-3 px-2 text-sm font-bold text-slate-800">{{ $u->full_name }}</td>
                        <td class="py-3 px-2 text-xs text-slate-500" dir="ltr">{{ $u->email }}</td>
                        <td class="py-3 px-2">
                            @php
                                $roleMap = ['admin' => ['👑 إداري','blue'], 'craftsman' => ['🔧 حرفي','amber'], 'client' => ['👤 عميل','emerald']];
                                $r = $roleMap[$u->role] ?? ['?','slate'];
                            @endphp
                            <span class="bg-{{ $r[1] }}-100 text-{{ $r[1] }}-700 px-2 py-1 rounded-lg text-xs font-bold">{{ $r[0] }}</span>
                        </td>
                        <td class="py-3 px-2">
                            @if($u->is_active)
                                <span class="text-emerald-600 text-xs font-bold">✅ نشط</span>
                            @else
                                <span class="text-red-600 text-xs font-bold">❌ غير نشط</span>
                            @endif
                        </td>
                        <td class="py-3 px-2">
                            @if($u->role !== 'admin')
                                <form action="{{ route('admin.users.destroy', $u) }}" method="POST" style="display:inline" onsubmit="return confirm('حذف المستخدم؟')">
                                    @csrf @method('DELETE')
                                    <button class="w-8 h-8 rounded-lg bg-red-100 hover:bg-red-200 text-red-600 transition-all">
                                        <i class="fa-solid fa-trash text-xs"></i>
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
