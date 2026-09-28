@extends('layouts.admin')
@section('title', 'النسخ الاحتياطي')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl lg:text-3xl font-black text-slate-800">
        <i class="fa-solid fa-database text-gold-500"></i> النسخ الاحتياطي
    </h1>
    <p class="text-slate-500 text-sm mt-1">إدارة النسخ الاحتياطية لقاعدة البيانات</p>
</div>

{{-- تحذير --}}
<div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 mb-5 flex items-start gap-3">
    <i class="fa-solid fa-triangle-exclamation text-amber-500 text-lg mt-0.5"></i>
    <div class="text-sm text-amber-800">
        <strong>⚠️ تنبيه:</strong> الاستعادة ستحذف جميع البيانات الحالية وتستبدلها ببيانات النسخة.
        يتم إنشاء <strong>نسخة أمان تلقائية</strong> قبل كل استعادة.
    </div>
</div>

{{-- زر الإنشاء --}}
<div class="bg-white rounded-3xl p-6 shadow-soft border border-slate-100 mb-5">
    <form method="POST" action="{{ route('admin.backup.create') }}">
        @csrf
        <button type="submit" class="px-8 py-4 rounded-2xl text-white font-black"
                style="background: linear-gradient(135deg, #D4A24C, #B8873A);">
            <i class="fa-solid fa-plus"></i> إنشاء نسخة جديدة
        </button>
    </form>
</div>

{{-- قائمة النسخ --}}
<div class="bg-white rounded-3xl p-6 shadow-soft border border-slate-100">
    <h3 class="font-black text-slate-800 mb-4">النسخ المتاحة ({{ $files->count() }})</h3>

    @if($files->isEmpty())
        <div class="text-center py-12">
            <i class="fa-solid fa-folder-open text-5xl text-slate-300 mb-3"></i>
            <p class="text-slate-500">لا توجد نسخ احتياطية</p>
        </div>
    @else
        <div class="space-y-2">
            @foreach($files as $f)
                <div class="flex items-center justify-between p-4 rounded-2xl bg-slate-50 border border-slate-100 flex-wrap gap-3">
                    <div class="flex items-center gap-3 flex-1 min-w-0">
                        <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid fa-file-code"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="font-bold text-slate-800 text-sm truncate">{{ $f['name'] }}</p>
                            <p class="text-xs text-slate-500 mt-1">
                                <i class="fa-solid fa-database"></i> {{ $f['size'] }}
                                · <i class="fa-regular fa-clock"></i> {{ $f['date'] }}
                            </p>
                        </div>
                    </div>
                    <div class="flex gap-2 flex-wrap">
                        {{-- معاينة --}}
                        <button type="button"
                                onclick="previewBackup('{{ $f['name'] }}')"
                                class="px-4 py-2 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold text-xs">
                            <i class="fa-solid fa-eye"></i>
                        </button>

                        {{-- استعادة --}}
                        <button type="button"
                                onclick="openRestoreModal('{{ $f['name'] }}')"
                                class="px-4 py-2 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-xs">
                            <i class="fa-solid fa-rotate"></i> استعادة
                        </button>

                        {{-- تحميل --}}
                        <a href="{{ route('admin.backup.download', $f['name']) }}"
                           class="px-4 py-2 rounded-xl bg-blue-500 hover:bg-blue-600 text-white font-bold text-xs">
                            <i class="fa-solid fa-download"></i>
                        </a>

                        {{-- حذف --}}
                        <form action="{{ route('admin.backup.destroy', $f['name']) }}" method="POST" style="display:inline"
                              onsubmit="return confirm('حذف هذه النسخة نهائياً؟')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2 rounded-xl bg-red-500 hover:bg-red-600 text-white font-bold text-xs">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

{{-- Modal: معاينة --}}
<div id="previewModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur z-50 items-center justify-center p-4">
    <div class="bg-white rounded-3xl p-6 max-w-md w-full">
        <h3 class="font-black text-lg text-slate-800 mb-4">
            <i class="fa-solid fa-info-circle text-blue-500"></i> تفاصيل النسخة
        </h3>
        <div id="previewContent" class="space-y-2 text-sm"></div>
        <button onclick="closePreview()" class="w-full mt-4 py-3 rounded-2xl bg-slate-100 hover:bg-slate-200 font-bold text-sm">
            إغلاق
        </button>
    </div>
</div>

{{-- Modal: استعادة --}}
<div id="restoreModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur z-50 items-center justify-center p-4">
    <div class="bg-white rounded-3xl p-6 max-w-md w-full">
        <div class="text-center mb-4">
            <div class="w-16 h-16 mx-auto rounded-full bg-red-100 flex items-center justify-center mb-3">
                <i class="fa-solid fa-triangle-exclamation text-red-500 text-2xl"></i>
            </div>
            <h3 class="font-black text-lg text-red-700">تأكيد الاستعادة</h3>
        </div>

        <div class="bg-red-50 border border-red-200 rounded-xl p-3 text-xs text-red-800 mb-4">
            <p class="font-bold mb-2">⚠️ تحذير خطير:</p>
            <ul class="list-disc list-inside space-y-1">
                <li>سيتم حذف جميع البيانات الحالية</li>
                <li>سيتم استبدالها ببيانات النسخة المختارة</li>
                <li>سيتم إنشاء نسخة أمان تلقائياً قبل التنفيذ</li>
            </ul>
        </div>

        <form method="POST" action="{{ route('admin.backup.restore') }}">
            @csrf
            <input type="hidden" name="file" id="restoreFile">

            <label class="block text-xs font-bold text-slate-600 mb-2">
                اكتب كلمة <code class="bg-red-100 text-red-700 px-2 py-0.5 rounded font-black">RESTORE</code> للتأكيد:
            </label>
            <input type="text" name="confirm" id="restoreConfirm"
                   placeholder="RESTORE" autocomplete="off" required
                   class="w-full px-4 py-3 rounded-2xl border-2 border-slate-200 focus:border-red-400 outline-none font-mono text-center font-bold tracking-widest">

            <div class="flex gap-3 mt-4">
                <button type="button" onclick="closeRestoreModal()"
                        class="flex-1 py-3 rounded-2xl bg-slate-100 hover:bg-slate-200 font-bold text-sm">
                    إلغاء
                </button>
                <button type="submit" id="restoreSubmitBtn" disabled
                        class="flex-1 py-3 rounded-2xl bg-red-500 hover:bg-red-600 text-white font-bold text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fa-solid fa-rotate"></i> استعادة
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
function previewBackup(file) {
    fetch('{{ url('admin/backup/preview') }}/' + encodeURIComponent(file))
        .then(r => r.json())
        .then(data => {
            if (data.error) { alert(data.error); return; }
            document.getElementById('previewContent').innerHTML = `
                <div class="flex justify-between"><span class="text-slate-500">الملف:</span><strong class="font-mono text-xs">${data.name}</strong></div>
                <div class="flex justify-between"><span class="text-slate-500">الحجم:</span><strong>${data.size}</strong></div>
                <div class="flex justify-between"><span class="text-slate-500">التاريخ:</span><strong>${data.date}</strong></div>
                <div class="flex justify-between"><span class="text-slate-500">عدد الجداول:</span><strong class="text-blue-600">${data.tables_count}</strong></div>
                <div class="flex justify-between"><span class="text-slate-500">عدد الجمل (INSERT):</span><strong class="text-emerald-600">${data.inserts_count}</strong></div>
                <div class="mt-3 pt-3 border-t border-slate-100">
                    <p class="text-xs text-slate-500 mb-2">الجداول المتضمنة:</p>
                    <div class="flex flex-wrap gap-1 max-h-32 overflow-y-auto">
                        ${data.tables.map(t => `<span class="text-xs bg-slate-100 px-2 py-1 rounded-lg">${t}</span>`).join('')}
                    </div>
                </div>
            `;
            const m = document.getElementById('previewModal');
            m.classList.remove('hidden'); m.classList.add('flex');
        })
        .catch(() => alert('فشل جلب التفاصيل'));
}

function closePreview() {
    const m = document.getElementById('previewModal');
    m.classList.add('hidden'); m.classList.remove('flex');
}

function openRestoreModal(file) {
    document.getElementById('restoreFile').value = file;
    document.getElementById('restoreConfirm').value = '';
    document.getElementById('restoreSubmitBtn').disabled = true;
    const m = document.getElementById('restoreModal');
    m.classList.remove('hidden'); m.classList.add('flex');
}

function closeRestoreModal() {
    const m = document.getElementById('restoreModal');
    m.classList.add('hidden'); m.classList.remove('flex');
}

// تمكين زر الاستعادة فقط عند كتابة RESTORE
document.getElementById('restoreConfirm').addEventListener('input', function () {
    document.getElementById('restoreSubmitBtn').disabled = this.value !== 'RESTORE';
});

// إغلاق الـ modals عند الضغط خارجها
document.getElementById('previewModal').addEventListener('click', e => {
    if (e.target === e.currentTarget) closePreview();
});
document.getElementById('restoreModal').addEventListener('click', e => {
    if (e.target === e.currentTarget) closeRestoreModal();
});
</script>
@endpush
