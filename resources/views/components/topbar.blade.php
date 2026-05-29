<header class="h-14 border-b border-white/10 bg-ink-900/60 backdrop-blur-xl flex items-center px-3 md:px-5 gap-3">
    <button data-toggle="sidebar" class="md:hidden btn-ghost !px-2" aria-label="Toggle sidebar">
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M3 12h18M3 18h18"/></svg>
    </button>

    <div class="flex items-center gap-2">
        <button class="flex items-center gap-2 rounded-xl px-3 py-1.5 text-sm font-medium text-white bg-ink-800/70 border border-white/10 hover:bg-ink-700 transition">
            <span class="h-2 w-2 rounded-full bg-emerald-400 shadow-[0_0_8px_rgba(52,211,153,.8)]"></span>
            ABX GPT
            <svg class="h-4 w-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
        </button>
    </div>

    <div class="flex-1"></div>

    <button
        id="temporary-chat-btn"
        type="button"
        onclick="typeof startTemporaryChat === 'function' && startTemporaryChat()"
        class="flex items-center gap-1.5 px-2 sm:px-3 py-1.5 rounded-full text-xs font-medium border transition shrink-0
               text-slate-300 border-white/10 bg-ink-800/50 hover:bg-ink-700 hover:text-white"
        title="Temporary chat — not saved to your account">
        <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
        </svg>
        <span class="hidden min-[420px]:inline">Temporary</span>
    </button>

    @guest
        <div id="guest-limit-pill" class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-full bg-amber-500/10 border border-amber-500/20 text-amber-300 text-xs">
            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M13 2L3 14h7l-1 8 10-12h-7z"/></svg>
            <span><span id="guest-remaining-count" class="font-semibold">{{ max(0, 10 - ($guestMessageCount ?? 0)) }}</span> / 10 free messages remaining</span>
        </div>
    @endguest

    <button class="btn-ghost" data-theme-toggle title="Toggle theme">
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8z"/></svg>
    </button>
    <button class="btn-ghost" data-open-modal="settings-modal" title="Settings">
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 0 0 .3 1.8l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.7 1.7 0 0 0-1.8-.3 1.7 1.7 0 0 0-1 1.5V21a2 2 0 1 1-4 0v-.1a1.7 1.7 0 0 0-1-1.5"/></svg>
    </button>
    @guest
        <button id="topbar-signin-btn" class="btn-primary !py-2" data-open-modal="auth-modal" data-auth-view="login">Sign in</button>
    @endguest
</header>
