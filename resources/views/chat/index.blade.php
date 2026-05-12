@extends('layouts.app')

@section('title', 'ABX GPT — Chat')

@section('content')
    <div class="flex flex-col h-full">
        <div class="flex-1 overflow-hidden">
            {{-- Toggle empty-state vs history below --}}
            {{-- @include('chat.empty-state') --}}
            @include('chat.history')
        </div>
        @include('components.chat-input')
    </div>

@endsection
