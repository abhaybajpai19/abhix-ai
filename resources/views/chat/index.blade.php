@extends('layouts.app')

@section('title', 'Abhix AI - AI Assistant')

@section('content')
    <div class="flex flex-col h-full">
    <h1 class="shrink-0 text-center text-sm font-semibold text-slate-300 py-2 border-b border-white/10 bg-ink-900/40">
        Welcome to Abhix AI
    </h1>
    <div class="flex flex-col flex-1 min-h-0">
        <div class="flex-1 overflow-hidden">
            {{-- Toggle empty-state vs history below --}}
            {{-- @include('chat.empty-state') --}}
            @include('chat.history')
        </div>
        @include('components.chat-input')
    </div>
    </div>

@endsection
