@extends('layouts.admin')
@section('title', 'إدارة الشكاوى')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl lg:text-3xl font-black text-slate-800">
        <i class="fa-solid fa-triangle-exclamation text-red-500"></i> إدارة الشكاوى
    </h1>
</div>

<div class="grid grid-cols-2 lg:grid-cols-6 gap-3 mb-5">
    @foreach([
        ['pending','معلقة','fa-clock','red'],
        ['reviewing','قيد المراجعة','fa-magnifying-glass','blue'],
        ['resolved','محلولة','fa-circle-check','emerald'],
        ['rejected','مرفوضة','fa-xmark','slate'],
        ['today','اليوم','fa-calendar','purple'],
        ['total','الإجمالي','fa-list','slate'],
    ] as $s)
        <div class="bg-white rounded-2xl p-3 border border-slate-100 shadow-soft text-center">
            <p class="text-xl font-black text-{{ $s[3] }}-600">{{ $stats[$s[0]] }}</p>
            <p class="text-[10px] text-slate-500 font-bold">{{ $s[1] }}</p>
        </div>
    @endforeach
</div>

@if($complaints->isEmpty())
    <div class="bg-white rounded-3xl p-12 text-center shadow-soft border border-slate-100">
        <i class="fa-solid fa-face-smile text-5xl text-slate-300 mb-3"></i>
        <p class="text-slate-500">🎉 لا توجد شكاوى</p>
    </div>
@else
    <div class="space-y-3">
        @foreach($complaints as $c)
            @php
                $borderColor = ['pending'=>'border-r-red-500','reviewing'=>'border-r-amber-500','resolved'=>'border-r-emerald-500','rejected'=>'border-r-slate-400'][$c->status] ?? 'border-r-slate-200';
            @endphp
            <div class="bg-white rounded-3xl p-5 shadow-soft border border-slate-100 border-r-4 {{ $borderColor }}">
                <div class="flex items-start justify-between flex-wrap gap-3 mb-3">
                    <div>
                        <p class="font-bold text-slate-800 text-sm">
                            #{{ str_pad($c->id, 4, '0', STR_PAD_LEFT) }}
                            <span class="text-slate-400 font-normal text-xs">— الطلب #{{ str_pad($c->request_id, 4, '0', STR_PAD_LEFT) }}</span>
                        </p>
                    </div>
                    <span class="text-xs font-bold text-slate-500">{{ $c->created_at->format('Y-m-d g:i A') }}</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm mb-3">
                    <p><span class="text-slate-500">👤 العميل:</span> <strong>{{ $c->client->full_name ?? '' }}</strong></p>
                    <p><span class="text-slate-500">🔧 الحرفي:</span> <strong>{{ $c->craftsman->full_name ?? '' }}</strong></p>
                    <p><span class="text-slate-500">📌 السبب:</span> <span class="bg-red-100 text-red-700 px-2 py-0.5 rounded-lg text-xs font-bold">{{ $c->reason_label }}</span></p>
                </div>

                <div class="bg-slate-50 rounded-xl p-3 text-sm mb-3">
                    {{ $c->details }}
                </div>

                @if($c->admin_response)
                    <div class="bg-blue-50 border-r-3 border-blue-500 rounded-xl p-3 text-sm mb-3">
                        <strong class="text-blue-700 text-xs">💬 رد الإدارة:</strong>
                        <p class="mt-1">{{ $c->admin_response }}</p>
                    </div>
                @endif

                @if(in_array($c->status, ['pending', 'reviewing']))
                    <div class="flex flex-wrap gap-2">
                        <button onclick="openModal({{ $c->id }}, 'resolve')" class="px-4 py-2 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-xs">
                            <i class="fa-solid fa-check"></i> حل
                        </button>
                        <button onclick="openModal({{ $c->id }}, 'reject')" class="px-4 py-2 rounded-xl bg-red-500 hover:bg-red-600 text-white font-bold text-xs">
                            <i class="fa-solid fa-xmark"></i> رفض
                        </button>
                        <button onclick="openModal({{ $c->id }}, 'review')" class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs">
                            <i class="fa-solid fa-magnifying-glass"></i> مراجعة
                        </button>
                    </div>
                @endif
            </div>
        @endforeach
    </div>
@endif

{{-- Modal --}}
<div id="complaintModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur z-50 items-center justify-center p-4">
    <div class="bg-white rounded-3xl p-6 max-w-md w-full" onclick="event.stopPropagation()">
        <h3 id="modalTitle" class="font-black text-lg text-slate-800 mb-4">معالجة الشكوى</h3>
        <form method="POST" id="complaintForm">
            @csrf
            <input type="hidden" name="action" id="modalAction">
            <textarea name="admin_response" rows="4" placeholder="اكتب رد الإدارة..."
                      class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200 focus:border-gold-400 outline-none font-cairo text-sm"></textarea>
            <div class="flex gap-3 mt-4 justify-end">
                <button type="button" onclick="closeModal()" class="px-4 py-2 rounded-xl bg-slate-100 font-bold text-sm">إلغاء</button>
                <button type="submit" class="px-4 py-2 rounded-xl text-white font-bold text-sm" style="background: linear-gradient(135deg, #D4A24C, #B8873A);">تأكيد</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openModal(id, action) {
    document.getElementById('complaintForm').action = '/admin/complaints/' + id + '/action';
    document.getElementById('modalAction').value = action;
    const titles = { review: '📋 مراجعة', resolve: '✅ حل الشكوى', reject: '❌ رفض' };
    document.getElementById('modalTitle').textContent = titles[action] || 'معالجة';
    const m = document.getElementById('complaintModal');
    m.classList.remove('hidden'); m.classList.add('flex');
}
function closeModal() {
    const m = document.getElementById('complaintModal');
    m.classList.add('hidden'); m.classList.remove('flex');
}
document.getElementById('complaintModal').addEventListener('click', e => { if (e.target === e.currentTarget) closeModal(); });
</script>
@endpush
