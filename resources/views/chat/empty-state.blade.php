<div class="h-full flex flex-col items-center justify-center px-6 text-center animate-fade-in">
    <div class="h-16 w-16 rounded-2xl bg-gradient-to-br from-brand-400 to-fuchsia-500 grid place-items-center shadow-glow animate-float">
        <svg viewBox="0 0 24 24" class="h-8 w-8 text-white" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2l2.39 4.84L20 8l-4 3.9.94 5.5L12 14.77 7.06 17.4 8 11.9 4 8l5.61-1.16L12 2z"/></svg>
    </div>
    <h1 class="mt-6 text-3xl md:text-4xl font-bold tracking-tight">
        How can <span class="grad-text">Nova</span> help you today?
    </h1>
    <p class="mt-2 text-sm text-slate-400 max-w-md">Ask anything — from writing and brainstorming to coding and analysis.</p>

    <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 gap-3 w-full max-w-2xl">
        @php
            $prompts = [
                ['icon' => 'M12 2l2.39 4.84L20 8l-4 3.9.94 5.5L12 14.77 7.06 17.4 8 11.9 4 8l5.61-1.16L12 2z', 'title' => 'Brainstorm names', 'desc' => 'for a minimalist coffee brand'],
                ['icon' => 'M16 18l6-6-6-6M8 6l-6 6 6 6', 'title' => 'Explain code', 'desc' => 'walk me through a recursive function'],
                ['icon' => 'M21 15a4 4 0 0 1-4 4H8l-5 4V6a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4z', 'title' => 'Draft an email', 'desc' => 'to negotiate a salary raise'],
                ['icon' => 'M3 3v18h18M7 14l4-4 4 4 5-5', 'title' => 'Analyze data', 'desc' => 'summarize trends in my CSV'],
            ];
        @endphp
        @foreach($prompts as $p)
            <button class="group card p-4 text-left hover:border-brand-500/40 hover:bg-ink-700/60 transition">
                <div class="flex items-start gap-3">
                    <div class="h-9 w-9 rounded-xl bg-ink-700 grid place-items-center text-brand-300 group-hover:bg-brand-500/20 transition">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="{{ $p['icon'] }}"/></svg>
                    </div>
                    <div>
                        <div class="text-sm font-semibold text-white">{{ $p['title'] }}</div>
                        <div class="text-xs text-slate-400 mt-0.5">{{ $p['desc'] }}</div>
                    </div>
                </div>
            </button>
        @endforeach
    </div>
</div>
