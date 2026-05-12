@props(['message' => '', 'time' => 'just now'])
<div class="flex gap-4 animate-fade-in">
    <div class="flex-1"></div>
    <div class="max-w-[80%]">
        <div class="rounded-2xl rounded-tr-md px-4 py-3 bg-gradient-to-br from-brand-500 to-brand-700 text-white shadow-glow">
            <p class="text-[15px] leading-relaxed whitespace-pre-wrap">{{ $message }}</p>
        </div>
        <div class="mt-1 text-right text-[11px] text-slate-500">{{ $time }}</div>
    </div>
    <div class="h-9 w-9 shrink-0 rounded-full bg-gradient-to-br from-emerald-400 to-brand-500 grid place-items-center text-white text-sm font-bold">A</div>
</div>
