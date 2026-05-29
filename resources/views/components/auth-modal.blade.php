<div id="auth-modal" class="hidden fixed inset-0 z-50 grid place-items-center bg-black/70 backdrop-blur-md p-4 transition-opacity duration-200 opacity-0" data-nosnippet>
    <div class="card w-full max-w-md p-7 animate-pop-in relative">
        <button class="absolute top-3 right-3 btn-ghost !px-2" data-close-modal="auth-modal">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
        </button>

        <div data-auth-panel="login">
            <div class="text-center mb-6">
                <div class="mx-auto h-12 w-12 rounded-2xl bg-gradient-to-br from-brand-400 to-fuchsia-500 grid place-items-center shadow-glow">
                    <svg viewBox="0 0 24 24" class="h-6 w-6 text-white" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2l2.39 4.84L20 8l-4 3.9.94 5.5L12 14.77 7.06 17.4 8 11.9 4 8l5.61-1.16L12 2z"/></svg>
                </div>
                <h2 class="mt-4 text-xl font-bold text-white">Welcome back</h2>
                <p class="text-sm text-slate-400">Sign in to continue your conversation</p>
            </div>
            <div id="auth-login-errors" class="hidden mb-3 text-xs rounded-lg border border-rose-400/30 bg-rose-500/10 p-3 text-rose-200"></div>
            <form id="login-form" class="space-y-3">
                @csrf
                <div>
                    <label class="text-xs font-medium text-slate-400">Email</label>
                    <input type="email" name="email" placeholder="you@example.com" class="mt-1 w-full bg-ink-800/70 border border-white/10 rounded-xl px-3 py-2.5 text-sm ring-focus" />
                </div>
                <div>
                    <label class="text-xs font-medium text-slate-400">Password</label>
                    <input type="password" name="password" placeholder="••••••••" class="mt-1 w-full bg-ink-800/70 border border-white/10 rounded-xl px-3 py-2.5 text-sm ring-focus" />
                </div>
                <div class="flex items-center justify-between text-xs text-slate-400">
                    <label class="flex items-center gap-2"><input type="checkbox" name="remember" class="rounded bg-ink-700 border-white/10"/> Remember me</label>
                    <button type="button" data-switch-auth="forgot" class="text-brand-300 hover:text-brand-200">Forgot password?</button>
                </div>
                <input type="hidden" name="migrate_guest_chats" value="1">
                <button class="btn-primary w-full justify-center !py-2.5">Sign in</button>
            </form>
            <p class="text-center text-xs text-slate-400 mt-5">Don't have an account? <button type="button" data-switch-auth="register" class="text-brand-300 hover:text-brand-200 font-semibold">Create one</button></p>
        </div>

        <div data-auth-panel="register" class="hidden">
            <div class="text-center mb-6">
                <div class="mx-auto h-12 w-12 rounded-2xl bg-gradient-to-br from-brand-400 to-fuchsia-500 grid place-items-center shadow-glow">
                    <svg viewBox="0 0 24 24" class="h-6 w-6 text-white" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
                </div>
                <h2 class="mt-4 text-xl font-bold text-white">Create account</h2>
                <p class="text-sm text-slate-400">Save chats permanently and remove limits</p>
            </div>
            <div id="auth-register-errors" class="hidden mb-3 text-xs rounded-lg border border-rose-400/30 bg-rose-500/10 p-3 text-rose-200"></div>
            <form id="register-form" class="space-y-3">
                @csrf
                <div>
                    <label class="text-xs font-medium text-slate-400">Full name</label>
                    <input type="text" name="name" placeholder="Nova User" class="mt-1 w-full bg-ink-800/70 border border-white/10 rounded-xl px-3 py-2.5 text-sm ring-focus" />
                </div>
                <div>
                    <label class="text-xs font-medium text-slate-400">Email</label>
                    <input type="email" name="email" placeholder="you@example.com" class="mt-1 w-full bg-ink-800/70 border border-white/10 rounded-xl px-3 py-2.5 text-sm ring-focus" />
                </div>
                <div>
                    <label class="text-xs font-medium text-slate-400">Password</label>
                    <input type="password" name="password" placeholder="••••••••" class="mt-1 w-full bg-ink-800/70 border border-white/10 rounded-xl px-3 py-2.5 text-sm ring-focus" />
                </div>
                <div>
                    <label class="text-xs font-medium text-slate-400">Confirm password</label>
                    <input type="password" name="password_confirmation" placeholder="••••••••" class="mt-1 w-full bg-ink-800/70 border border-white/10 rounded-xl px-3 py-2.5 text-sm ring-focus" />
                </div>
                <input type="hidden" name="migrate_guest_chats" value="1">
                <button class="btn-primary w-full justify-center !py-2.5">Create account</button>
            </form>
            <p class="text-center text-xs text-slate-400 mt-5">Already have an account? <button type="button" data-switch-auth="login" class="text-brand-300 hover:text-brand-200 font-semibold">Sign in</button></p>
        </div>

        <div data-auth-panel="forgot" class="hidden">
            <div class="text-center mb-6">
                <div class="mx-auto h-12 w-12 rounded-2xl bg-gradient-to-br from-brand-400 to-fuchsia-500 grid place-items-center shadow-glow">
                    <svg viewBox="0 0 24 24" class="h-6 w-6 text-white" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 10v4m0 4h.01"/><circle cx="12" cy="12" r="10"/></svg>
                </div>
                <h2 class="mt-4 text-xl font-bold text-white">Reset password</h2>
                <p class="text-sm text-slate-400">We will email you a secure reset link</p>
            </div>
            <div id="auth-forgot-errors" class="hidden mb-3 text-xs rounded-lg border border-rose-400/30 bg-rose-500/10 p-3 text-rose-200"></div>
            <div id="auth-forgot-success" class="hidden mb-3 text-xs rounded-lg border border-emerald-400/30 bg-emerald-500/10 p-3 text-emerald-200"></div>
            <form id="forgot-form" class="space-y-3">
                @csrf
                <div>
                    <label class="text-xs font-medium text-slate-400">Email</label>
                    <input type="email" name="email" placeholder="you@example.com" class="mt-1 w-full bg-ink-800/70 border border-white/10 rounded-xl px-3 py-2.5 text-sm ring-focus" />
                </div>
                <button class="btn-primary w-full justify-center !py-2.5">Send reset link</button>
            </form>
            <p class="text-center text-xs text-slate-400 mt-5">Remembered your password? <button type="button" data-switch-auth="login" class="text-brand-300 hover:text-brand-200 font-semibold">Back to sign in</button></p>
        </div>
    </div>
</div>
