@php
    $isCraftsman = auth()->user()->isCraftsman();
    $layout      = $isCraftsman ? 'layouts.craftsman' : 'layouts.client';
    $prefix      = $isCraftsman ? 'craftsman' : 'client';
@endphp
@extends($layout)
@section('title', 'الرسائل')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl lg:text-3xl font-black text-slate-800">
        <i class="fa-solid fa-comments text-gold-500"></i> الرسائل
    </h1>
    <p class="text-slate-500 text-sm mt-1">{{ $requests->count() }} محادثة</p>
</div>

@if($requests->isEmpty())
    <div class="bg-white rounded-3xl p-12 text-center shadow-soft border border-slate-100">
        <i class="fa-solid fa-comment-dots text-5xl text-slate-300 mb-4"></i>
        <h3 class="font-bold text-slate-700 mb-2">لا توجد محادثات</h3>
        <p class="text-sm text-slate-500 mb-5">
            @if($isCraftsman)
                ستظهر هنا محادثاتك مع العملاء بعد قبول الطلبات
            @else
                ابدأ بمحادثة مع حرفي
            @endif
        </p>
        @if(!$isCraftsman)
            <a href="{{ route('client.search') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl text-white font-bold text-sm"
               style="background: linear-gradient(135deg, #D4A24C, #B8873A);">
                <i class="fa-solid fa-search"></i> ابحث عن حرفي
            </a>
        @endif
    </div>
@else
    <div class="space-y-3">
        @foreach($requests as $conv)
            @php
                $otherParty = $isCraftsman ? $conv->request->client : $conv->request->craftsman;
                $otherLabel = $isCraftsman ? 'عميل' : 'حرفي';
            @endphp
            <a href="{{ route($prefix . '.chat', $conv->request->id) }}"
               class="block bg-white rounded-3xl p-4 shadow-soft border border-slate-100 hover:shadow-lift hover:border-gold-300 transition-all {{ $conv->unread_count > 0 ? 'border-r-4 border-r-gold-500' : '' }}">
                <div class="flex items-center gap-4">
                    <img src="{{ $otherParty->avatar_url ?? asset('assets/images/logo.png') }}"
                         class="w-14 h-14 rounded-2xl object-cover border-2 border-gold-400">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between mb-1">
                            <h3 class="font-bold text-slate-800 truncate">{{ $otherParty->full_name ?? $otherLabel }}</h3>
                            @if($conv->last_message)
                                <span class="text-xs text-slate-400 shrink-0">{{ $conv->last_message->created_at->diffForHumans() }}</span>
                            @endif
                        </div>
                        <p class="text-xs text-slate-500 mb-1 truncate">
                            <i class="fa-solid fa-file-lines"></i> {{ $conv->request->title }}
                        </p>
                        <p class="text-sm text-slate-600 truncate">
                            @if($conv->last_message)
                                {{ Str::limit($conv->last_message->message, 50) }}
                            @else
                                <em class="text-slate-400 text-xs">لا توجد رسائل</em>
                            @endif
                        </p>
                    </div>
                    @if($conv->unread_count > 0)
                        <span class="w-6 h-6 bg-red-500 text-white rounded-full text-xs font-bold flex items-center justify-center">{{ $conv->unread_count }}</span>
                    @endif
                </div>
            </a>
        @endforeach
    </div>
@endif
@endsection
