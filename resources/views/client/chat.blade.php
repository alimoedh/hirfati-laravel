@php
    $isCraftsman = auth()->user()->isCraftsman();
    $layout      = $isCraftsman ? 'layouts.craftsman' : 'layouts.client';
    $prefix      = $isCraftsman ? 'craftsman' : 'client';
@endphp
@extends($layout)
@section('title', 'المحادثة')

@push('styles')
<style>
    .chat-bubble-sent { background: linear-gradient(135deg, #1E3A5F, #2D4E80); color: white; border-radius: 18px 18px 4px 18px; max-width: 75%; padding: 0.7rem 1rem; }
    .chat-bubble-received { background: #F1F5F9; color: #0F172A; border-radius: 18px 18px 18px 4px; max-width: 75%; padding: 0.7rem 1rem; }
</style>
@endpush

@section('content')
<div class="max-w-3xl mx-auto">
    <a href="{{ route($prefix . '.messages') }}" class="inline-flex items-center gap-2 text-slate-500 hover:text-gold-600 text-sm font-bold mb-3">
        <i class="fa-solid fa-arrow-right"></i> العودة للرسائل
    </a>

    <div class="bg-white rounded-3xl shadow-soft border border-slate-100 overflow-hidden" style="height: calc(100vh - 200px); display: flex; flex-direction: column;">
        {{-- Header --}}
        <div class="p-4 flex items-center justify-between" style="background: linear-gradient(135deg, #0F1E3C, #1E3A5F);">
            <div class="flex items-center gap-3">
                <div class="relative">
                    <img src="{{ $otherUser->avatar_url ?? asset('assets/images/logo.png') }}" class="w-12 h-12 rounded-full border-2 border-gold-400 object-cover">
                    <span class="absolute bottom-0 right-0 w-3 h-3 bg-emerald-400 border-2 border-white rounded-full"></span>
                </div>
                <div>
                    <h3 class="font-bold text-white">{{ $otherName }}</h3>
                    <p class="text-xs text-white/60">الطلب #{{ str_pad($request->id, 4, '0', STR_PAD_LEFT) }}</p>
                </div>
            </div>
            <a href="{{ route($prefix . '.live-stream.show', $request->id) }}" class="w-10 h-10 rounded-xl bg-white/10 hover:bg-white/20 flex items-center justify-center text-white">
                <i class="fa-solid fa-video"></i>
            </a>
        </div>

        {{-- Messages --}}
        <div id="chatBody" class="flex-1 overflow-y-auto p-5 space-y-3 bg-slate-50">
            @foreach($messages as $msg)
                <div class="flex {{ $msg->sender_id == auth()->id() ? 'justify-end' : 'justify-start' }}">
                    <div class="{{ $msg->sender_id == auth()->id() ? 'chat-bubble-sent' : 'chat-bubble-received' }}">
                        @if($msg->sender_id != auth()->id())
                            <p class="text-xs font-bold opacity-60 mb-1">{{ $msg->sender->full_name ?? '' }}</p>
                        @endif
                        @if($msg->is_voice && $msg->voice_url)
                            <audio controls class="h-8"><source src="{{ $msg->voice_url }}" type="audio/webm"></audio>
                        @else
                            <p class="text-sm">{{ $msg->message }}</p>
                        @endif
                        <p class="text-xs opacity-50 mt-1">{{ $msg->created_at->diffForHumans() }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Input --}}
        <div class="p-4 border-t border-slate-100 bg-white">
            <form id="chatForm" class="flex gap-2">
                <input type="text" id="msgInput" placeholder="اكتب رسالتك..."
                       class="flex-1 px-4 py-3 rounded-2xl border-2 border-slate-200 focus:border-gold-400 outline-none font-cairo text-sm">
                <button type="button" id="voiceBtn" class="w-12 h-12 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center hover:bg-blue-200">
                    <i class="fa-solid fa-microphone"></i>
                </button>
                <button type="submit" class="px-5 rounded-2xl text-white font-bold" style="background: linear-gradient(135deg, #D4A24C, #B8873A);">
                    <i class="fa-solid fa-paper-plane"></i>
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const chatBody = document.getElementById('chatBody');
chatBody.scrollTop = chatBody.scrollHeight;
let lastId = {{ $messages->last()->id ?? 0 }};
const REQ_ID = {{ $request->id }};
const OTHER_ID = {{ $otherUserId }};
const MY_ID = {{ auth()->id() }};
const ROUTE_PREFIX = '{{ $prefix }}';

document.getElementById('chatForm').addEventListener('submit', e => {
    e.preventDefault();
    const input = document.getElementById('msgInput');
    const msg = input.value.trim();
    if (!msg) return;
    const fd = new FormData();
    fd.append('request_id', REQ_ID);
    fd.append('receiver_id', OTHER_ID);
    fd.append('message', msg);
    fetch('/' + ROUTE_PREFIX + '/chat/send', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: fd
    })

   then(r => r.json()).then(res => {
        if (res.success) {
            append({ id: res.message_id, message: msg, sender_id: MY_ID, is_voice: 0, time_ago: 'الآن' }, true);
            lastId = res.message_id;   // ✅ ← هذا هو السطر المفقود
            input.value = '';
        }
    });

});

setInterval(() => {
    fetch('/' + ROUTE_PREFIX + '/chat/' + REQ_ID + '/fetch?last_id=' + lastId)
        .then(r => r.json())
        .then(d => {
            if (d.messages?.length) {
                d.messages.forEach(m => {
                    append(m, m.sender_id == MY_ID);
                    lastId = m.id;
                });
                chatBody.scrollTop = chatBody.scrollHeight;
            }
        })
        .catch(() => {});
}, 5000);

function append(m, sent) {
    const div = document.createElement('div');
    div.className = 'flex ' + (sent ? 'justify-end' : 'justify-start');
    div.innerHTML = `<div class="${sent ? 'chat-bubble-sent' : 'chat-bubble-received'}">
        ${!sent ? `<p class="text-xs font-bold opacity-60 mb-1">${m.sender_name || ''}</p>` : ''}
        ${m.is_voice && m.voice_url ? `<audio controls class="h-8"><source src="${m.voice_url}"></audio>` : `<p class="text-sm">${m.message}</p>`}
        <p class="text-xs opacity-50 mt-1">${m.time_ago || 'الآن'}</p></div>`;
    chatBody.appendChild(div);
    chatBody.scrollTop = chatBody.scrollHeight;
}
</script>
@endpush
