@props(['message' => '', 'time' => 'just now', 'code' => null])
<div class="flex gap-4 animate-fade-in">
    <div class="h-9 w-9 shrink-0 rounded-xl bg-gradient-to-br from-brand-400 to-fuchsia-500 grid place-items-center text-white shadow-glow">
        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2l2.39 4.84L20 8l-4 3.9.94 5.5L12 14.77 7.06 17.4 8 11.9 4 8l5.61-1.16L12 2z"/></svg>
    </div>
    <div class="max-w-[80%] flex-1">
        <div class="card px-4 py-3">
            <div class="flex items-center gap-2 text-xs text-slate-400 mb-2">
                <span class="font-semibold text-white">ABX GPT</span>
                <span>·</span>
                <span>{{ $time }}</span>
            </div>
            <div class="prose-invert text-[15px] leading-relaxed text-slate-200 space-y-3">
                {!! $message !!}
                @if($code)
                    <div class="relative mt-3 rounded-xl border border-white/10 bg-ink-950 overflow-hidden">
                        <div class="flex items-center justify-between px-3 py-2 bg-ink-800 text-xs text-slate-400 border-b border-white/10">
                            <span>{{ $code['lang'] ?? 'code' }}</span>
                            <button class="btn-ghost !py-1 !px-2 !text-[11px]" onclick="navigator.clipboard.writeText(this.closest('.relative').querySelector('pre').innerText); showToast('Code copied')">
                                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                                Copy
                            </button>
                        </div>
                        <pre class="p-4 text-[13px] font-mono text-slate-200 overflow-x-auto"><code>{{ $code['content'] }}</code></pre>
                    </div>
                @endif
            </div>
            <div class="mt-3 flex items-center gap-1 text-slate-400">
                <button class="btn-ghost !px-2 !py-1" title="Copy" onclick="showToast('Copied')"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg></button>
                <button class="btn-ghost !px-2 !py-1" title="Good response"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 9V5a3 3 0 0 0-6 0v4H5l-2 9h14l2-9h-5z"/></svg></button>
                <button class="btn-ghost !px-2 !py-1" title="Bad response"><svg class="h-4 w-4 rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 9V5a3 3 0 0 0-6 0v4H5l-2 9h14l2-9h-5z"/></svg></button>
                <button class="btn-ghost !px-2 !py-1" title="Regenerate"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M23 4v6h-6M1 20v-6h6"/><path d="M3.5 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.65 4.36A9 9 0 0 0 20.5 15"/></svg></button>
            </div>
        </div>
    </div>
</div>
