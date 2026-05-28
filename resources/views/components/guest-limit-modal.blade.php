<div id="guest-limit-modal" class="hidden fixed inset-0 z-50 grid place-items-center bg-black/80 backdrop-blur-xl p-4 transition-opacity duration-300 opacity-0">
    <div class="card w-full max-w-lg p-8 text-center animate-pop-in relative overflow-hidden">
        <div class="pointer-events-none absolute -top-20 -right-20 h-60 w-60 rounded-full bg-brand-500/30 blur-3xl"></div>
        <div class="pointer-events-none absolute -bottom-20 -left-20 h-60 w-60 rounded-full bg-fuchsia-500/20 blur-3xl"></div>

        <div class="mx-auto h-16 w-16 rounded-2xl bg-gradient-to-br from-brand-400 to-fuchsia-500 grid place-items-center shadow-glow animate-float">
            <svg viewBox="0 0 24 24" class="h-8 w-8 text-white" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 15v2m0-12a4 4 0 0 0-4 4v3h8V9a4 4 0 0 0-4-4z"/><rect x="4" y="11" width="16" height="11" rx="2"/></svg>
        </div>
        <h2 class="mt-5 text-2xl font-bold text-white">You've reached your free limit</h2>
        <p class="mt-2 text-sm text-slate-400 max-w-sm mx-auto">You've used all 10 free messages without an account. Sign in or create a free account to continue — your guest chats will be saved to your account.</p>
        <div class="mt-6 flex flex-col sm:flex-row gap-2 justify-center">
            <button class="btn-primary !px-6" onclick="showAuthPanel('login'); closeModal('guest-limit-modal'); openModal('auth-modal');">Sign in</button>
            <button class="btn-ghost !border !border-white/10" onclick="showAuthPanel('register'); closeModal('guest-limit-modal'); openModal('auth-modal');">Create account</button>
        </div>
        <p class="mt-4 text-[11px] text-slate-500">No credit card required · Cancel anytime</p>
    </div>
</div>
