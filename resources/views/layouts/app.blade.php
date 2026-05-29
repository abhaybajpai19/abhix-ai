<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Abhix AI - AI Assistant')</title>
    <meta name="description" content="Abhix AI is a powerful AI assistant built with Laravel.">
    @include('components.seo-head')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        (() => {
            const storedTheme = localStorage.getItem('nova-theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            const shouldUseDark = storedTheme ? storedTheme === 'dark' : prefersDark;
            document.documentElement.classList.toggle('dark', shouldUseDark);
        })();
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui'],
                        mono: ['JetBrains Mono', 'ui-monospace'],
                    },
                    colors: {
                        ink: {
                            950: '#07080c',
                            900: '#0b0d12',
                            800: '#11141b',
                            700: '#171b24',
                            600: '#1f2430',
                            500: '#2a2f3d',
                        },
                        brand: {
                            300: '#a5b4ff',
                            400: '#7c8cff',
                            500: '#5b6cff',
                            600: '#4754e6',
                            700: '#3a45c2',
                        },
                    },
                    boxShadow: {
                        glow: '0 0 0 1px rgba(124,140,255,.25), 0 10px 40px -10px rgba(91,108,255,.45)',
                        soft: '0 10px 30px -10px rgba(0,0,0,.5)',
                    },
                    keyframes: {
                        fadeIn: { '0%': { opacity: 0, transform: 'translateY(6px)' }, '100%': { opacity: 1, transform: 'translateY(0)' } },
                        pop:    { '0%': { opacity: 0, transform: 'scale(.96)' }, '100%': { opacity: 1, transform: 'scale(1)' } },
                        shimmer:{ '0%':{backgroundPosition:'-400px 0'},'100%':{backgroundPosition:'400px 0'}},
                        blink:  { '0%,100%':{opacity:.2},'50%':{opacity:1}},
                        float:  { '0%,100%':{transform:'translateY(0)'},'50%':{transform:'translateY(-4px)'}},
                    },
                    animation: {
                        'fade-in': 'fadeIn .35s ease-out both',
                        'pop-in': 'pop .25s ease-out both',
                        shimmer: 'shimmer 1.4s linear infinite',
                        blink: 'blink 1.2s ease-in-out infinite',
                        float: 'float 3s ease-in-out infinite',
                    },
                },
            },
        }
    </script>
    <style type="text/tailwindcss">
        @layer base {
            html, body { height: 100%; }
            body { @apply bg-ink-950 text-slate-200 font-sans antialiased; }
            ::selection { @apply bg-brand-500/40 text-white; }
            ::-webkit-scrollbar { width: 10px; height: 10px; }
            ::-webkit-scrollbar-thumb { @apply bg-ink-600 rounded-full; }
            ::-webkit-scrollbar-thumb:hover { @apply bg-ink-500; }
        }
        @layer components {
            .glass { @apply bg-white/5 backdrop-blur-xl border border-white/10; }
            .grad-text { @apply bg-clip-text text-transparent bg-gradient-to-r from-brand-300 via-fuchsia-300 to-brand-400; }
            .btn-primary { @apply inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-semibold text-white bg-gradient-to-br from-brand-500 to-brand-700 hover:from-brand-400 hover:to-brand-600 shadow-glow transition-all active:scale-[.98]; }
            .btn-ghost { @apply inline-flex items-center justify-center gap-2 rounded-xl px-3 py-2 text-sm text-slate-300 hover:text-white hover:bg-white/5 transition; }
            .card { @apply rounded-2xl border border-white/10 bg-ink-800/60 backdrop-blur-md shadow-soft; }
            .ring-focus { @apply focus:outline-none focus:ring-2 focus:ring-brand-500/60; }
        }
    </style>
    <style>
        html:not(.dark) body {
            background: #dfe6f0 !important;
            color: #1e293b !important;
        }
        html:not(.dark) .pointer-events-none.fixed.inset-0.-z-10 {
            opacity: 0.45;
        }
        html:not(.dark) .pointer-events-none.fixed.inset-0.-z-10 > div {
            opacity: 0.55;
        }
        html:not(.dark) #app-sidebar,
        html:not(.dark) header {
            background: rgba(255, 255, 255, 0.82) !important;
            backdrop-filter: blur(12px);
            border-color: rgba(15, 23, 42, 0.08) !important;
        }
        html:not(.dark) main {
            background: transparent;
        }
        html:not(.dark) .card {
            background: rgba(255, 255, 255, 0.94) !important;
            border-color: rgba(15, 23, 42, 0.1) !important;
            box-shadow: 0 8px 24px -12px rgba(15, 23, 42, 0.15) !important;
        }
        html:not(.dark) [class*="bg-ink-8"],
        html:not(.dark) [class*="bg-ink-9"] {
            background-color: rgba(248, 250, 252, 0.95) !important;
        }
        html:not(.dark) [class*="bg-ink-7"],
        html:not(.dark) [class*="bg-ink-6"] {
            background-color: rgba(241, 245, 249, 0.98) !important;
        }
        html:not(.dark) [class*="text-slate-4"],
        html:not(.dark) [class*="text-slate-5"] {
            color: #64748b !important;
        }
        html:not(.dark) [class*="text-slate-3"] {
            color: #475569 !important;
        }
        html:not(.dark) .text-white {
            color: #0f172a !important;
        }
        html:not(.dark) .border-white\/10,
        html:not(.dark) .border-white\/15 {
            border-color: rgba(15, 23, 42, 0.1) !important;
        }
        html:not(.dark) .btn-ghost {
            color: #475569 !important;
        }
        html:not(.dark) .btn-ghost:hover {
            background: rgba(15, 23, 42, 0.05) !important;
            color: #0f172a !important;
        }
        html:not(.dark) textarea,
        html:not(.dark) input:not([type="checkbox"]) {
            color: #0f172a !important;
        }
        html:not(.dark) textarea::placeholder {
            color: #94a3b8 !important;
        }
        html:not(.dark) .bg-brand-500 {
            background: linear-gradient(to bottom right, #5b6cff, #4754e6) !important;
            color: #fff !important;
        }
        html:not(.dark) .bg-brand-500.text-white,
        html:not(.dark) .bg-brand-500 .text-white {
            color: #fff !important;
        }
        html:not(.dark) [data-chat-message] .bg-ink-800 {
            background-color: #f1f5f9 !important;
            color: #0f172a !important;
        }
        html:not(.dark) #temporary-chat-banner > div {
            background: rgba(139, 92, 246, 0.08) !important;
            border-color: rgba(139, 92, 246, 0.2) !important;
            color: #5b21b6 !important;
        }
    </style>
    @stack('head')
</head>
<body class="overflow-hidden">
    <div class="pointer-events-none fixed inset-0 -z-10 overflow-hidden">
        <div class="absolute -top-40 -left-40 h-[480px] w-[480px] rounded-full bg-brand-600/20 blur-3xl"></div>
        <div class="absolute -bottom-40 -right-40 h-[520px] w-[520px] rounded-full bg-fuchsia-600/10 blur-3xl"></div>
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_50%_-10%,rgba(124,140,255,.08),transparent_60%)]"></div>
    </div>

    <div class="flex h-screen w-screen">
        @include('components.sidebar')
        <div class="flex flex-1 flex-col min-w-0">
            @include('components.topbar')
            <main class="flex-1 overflow-hidden relative">
                @yield('content')
            </main>
        </div>
    </div>

    @include('components.auth-modal')
    @include('components.guest-limit-modal')
    @include('components.settings-modal')
    @include('components.toast')

    <script>
        // ----- Sidebar toggle (mobile) -----
        const sidebar = document.getElementById('app-sidebar');
        const backdrop = document.getElementById('sidebar-backdrop');
        document.querySelectorAll('[data-toggle="sidebar"]').forEach(b => b.addEventListener('click', () => {
            sidebar.classList.toggle('-translate-x-full');
            backdrop.classList.toggle('hidden');
        }));
        backdrop?.addEventListener('click', () => {
            sidebar.classList.add('-translate-x-full');
            backdrop.classList.add('hidden');
        });

        // ----- Modal helpers -----
        window.openModal = (id) => {
            const m = document.getElementById(id);
            if (!m) return;
            m.classList.remove('hidden');
            requestAnimationFrame(() => m.classList.add('opacity-100'));
        };
        window.closeModal = (id) => {
            const m = document.getElementById(id);
            if (!m) return;
            m.classList.add('hidden');
            m.classList.remove('opacity-100');
        };

        const modalIds = ['settings-modal', 'auth-modal', 'guest-limit-modal', 'confirm-delete-modal', 'rename-chat-modal', 'confirm-logout-modal'];

        window.showAlert = (title, text, icon = 'success') => {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title,
                    text,
                    icon,
                    confirmButtonColor: '#5b6cff',
                    background: document.documentElement.classList.contains('dark') ? '#11141b' : '#fff',
                    color: document.documentElement.classList.contains('dark') ? '#e2e8f0' : '#0f172a',
                });
                return;
            }
            showToast(text);
        };

        window.showConfirm = async (title, text, icon = 'warning') => {
            if (typeof Swal !== 'undefined') {
                const result = await Swal.fire({
                    title,
                    text,
                    icon,
                    showCancelButton: true,
                    confirmButtonColor: '#5b6cff',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'Cancel',
                    background: document.documentElement.classList.contains('dark') ? '#11141b' : '#fff',
                    color: document.documentElement.classList.contains('dark') ? '#e2e8f0' : '#0f172a',
                });
                return result.isConfirmed;
            }
            return confirm(text);
        };

        window.updateProfileUi = (user) => {
            if (!user) return;

            const initials = (user.name || 'User')
                .split(' ')
                .slice(0, 2)
                .map((part) => part[0] || '')
                .join('')
                .toUpperCase();

            const avatar = document.getElementById('sidebar-avatar');
            const name = document.getElementById('sidebar-user-name');
            const email = document.getElementById('sidebar-user-email');
            const profileMenuName = document.getElementById('profile-menu-name');
            const profileMenuEmail = document.getElementById('profile-menu-email');
            const settingsDisplayName = document.getElementById('settings-display-name');
            const settingsDisplayEmail = document.getElementById('settings-display-email');

            if (name) name.textContent = user.name || 'User';
            if (email) email.textContent = user.email || '';
            if (profileMenuName) profileMenuName.textContent = user.name || 'User';
            if (profileMenuEmail) profileMenuEmail.textContent = user.email || '';
            if (settingsDisplayName) settingsDisplayName.textContent = user.name || 'User';
            if (settingsDisplayEmail) settingsDisplayEmail.textContent = user.email || '';

            if (avatar) {
                if (user.profile_photo_url) {
                    avatar.className = 'h-9 w-9 rounded-full overflow-hidden bg-transparent';
                    avatar.innerHTML = `<img src="${user.profile_photo_url}?t=${Date.now()}" alt="Profile photo" class="h-full w-full object-cover" />`;
                } else {
                    avatar.className = 'h-9 w-9 rounded-full overflow-hidden bg-gradient-to-br from-emerald-400 to-brand-500 grid place-items-center text-white text-sm font-bold';
                    avatar.textContent = initials || 'U';
                }
            }
        };

        document.addEventListener('keydown', (event) => {
            if (event.key !== 'Escape') return;
            modalIds.forEach((id) => {
                const modal = document.getElementById(id);
                if (modal && !modal.classList.contains('hidden')) {
                    closeModal(id);
                }
            });
        });

        document.querySelectorAll('[data-open-modal]').forEach(b => b.addEventListener('click', () => openModal(b.dataset.openModal)));
        document.querySelectorAll('[data-close-modal]').forEach(b => b.addEventListener('click', () => closeModal(b.dataset.closeModal)));

        // ----- Toast -----
        window.showToast = (msg) => {
            const t = document.getElementById('toast');
            t.querySelector('[data-toast-msg]').textContent = msg;
            t.classList.remove('translate-y-10','opacity-0','pointer-events-none');
            clearTimeout(window.__toastT);
            window.__toastT = setTimeout(() => t.classList.add('translate-y-10','opacity-0','pointer-events-none'), 2400);
        };

        // ----- Theme toggle -----
        document.querySelectorAll('[data-theme-toggle]').forEach(b => b.addEventListener('click', () => {
            document.documentElement.classList.toggle('dark');
            localStorage.setItem('nova-theme', document.documentElement.classList.contains('dark') ? 'dark' : 'light');
            showToast(document.documentElement.classList.contains('dark') ? 'Dark mode enabled' : 'Light mode enabled');
        }));

        // ----- Profile dropdown -----
        const profileBtn = document.getElementById('profile-btn');
        const profileMenu = document.getElementById('profile-menu');
        profileBtn?.addEventListener('click', (e) => { e.stopPropagation(); profileMenu.classList.toggle('hidden'); });
        document.addEventListener('click', () => profileMenu?.classList.add('hidden'));

        // ----- Auth modal behavior -----
        const authPanels = document.querySelectorAll('[data-auth-panel]');
        const authErrorBoxes = [
            document.getElementById('auth-login-errors'),
            document.getElementById('auth-register-errors'),
            document.getElementById('auth-forgot-errors'),
        ];
        const forgotSuccessBox = document.getElementById('auth-forgot-success');

        function hideAuthMessages() {
            authErrorBoxes.forEach((box) => {
                if (!box) return;
                box.classList.add('hidden');
                box.textContent = '';
            });
            if (forgotSuccessBox) {
                forgotSuccessBox.classList.add('hidden');
                forgotSuccessBox.textContent = '';
            }
        }

        function showAuthPanel(panel) {
            authPanels.forEach((item) => {
                item.classList.toggle('hidden', item.dataset.authPanel !== panel);
            });
            hideAuthMessages();
        }
        window.showAuthPanel = showAuthPanel;

        function formatErrors(errors) {
            if (!errors) return 'Something went wrong. Please try again.';
            return Object.values(errors).flat().join(' ');
        }

        async function postAuthForm(url, form) {
            const formData = new FormData(form);
            const payload = Object.fromEntries(formData.entries());
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: JSON.stringify(payload),
            });

            const data = await response.json();
            if (!response.ok) {
                throw data;
            }

            return data;
        }

        function updateAuthUiFromUser(user) {
            const topbarSignInButton = document.getElementById('topbar-signin-btn');
            if (topbarSignInButton) topbarSignInButton.classList.add('hidden');

            const avatar = document.getElementById('sidebar-avatar');
            const name = document.getElementById('sidebar-user-name');
            const email = document.getElementById('sidebar-user-email');
            const profileMenuName = document.getElementById('profile-menu-name');
            const profileMenuEmail = document.getElementById('profile-menu-email');
            const guestLimitPill = document.getElementById('guest-limit-pill');

            const initials = (user?.name || 'User')
                .split(' ')
                .slice(0, 2)
                .map((part) => part[0] || '')
                .join('')
                .toUpperCase();

            if (avatar) avatar.textContent = initials || 'U';
            if (name) name.textContent = user?.name || 'User';
            if (email) email.textContent = user?.email || '';
            if (profileMenuName) profileMenuName.textContent = user?.name || 'User';
            if (profileMenuEmail) profileMenuEmail.textContent = user?.email || '';
            if (guestLimitPill) guestLimitPill.classList.add('hidden');
        }

        document.querySelectorAll('[data-switch-auth]').forEach((button) => {
            button.addEventListener('click', () => showAuthPanel(button.dataset.switchAuth));
        });

        document.querySelectorAll('[data-auth-view]').forEach((button) => {
            button.addEventListener('click', () => {
                const targetView = button.dataset.authView;
                if (targetView) showAuthPanel(targetView);
            });
        });

        const loginForm = document.getElementById('login-form');
        loginForm?.addEventListener('submit', async (event) => {
            event.preventDefault();
            hideAuthMessages();
            try {
                const data = await postAuthForm('{{ route('login') }}', loginForm);
                updateAuthUiFromUser(data.user);
                closeModal('auth-modal');
                showToast(data.message || 'Signed in successfully');
                window.setTimeout(() => window.location.reload(), 350);
            } catch (errorData) {
                const box = document.getElementById('auth-login-errors');
                if (box) {
                    box.textContent = formatErrors(errorData.errors);
                    box.classList.remove('hidden');
                }
            }
        });

        const registerForm = document.getElementById('register-form');
        registerForm?.addEventListener('submit', async (event) => {
            event.preventDefault();
            hideAuthMessages();
            try {
                const data = await postAuthForm('{{ route('register') }}', registerForm);
                updateAuthUiFromUser(data.user);
                closeModal('auth-modal');
                closeModal('guest-limit-modal');
                showToast(data.message || 'Account created successfully');
                window.setTimeout(() => window.location.reload(), 350);
            } catch (errorData) {
                const box = document.getElementById('auth-register-errors');
                if (box) {
                    box.textContent = formatErrors(errorData.errors);
                    box.classList.remove('hidden');
                }
            }
        });

        const forgotForm = document.getElementById('forgot-form');
        forgotForm?.addEventListener('submit', async (event) => {
            event.preventDefault();
            hideAuthMessages();
            try {
                const data = await postAuthForm('{{ route('password.email') }}', forgotForm);
                if (forgotSuccessBox) {
                    forgotSuccessBox.textContent = data.message || 'Reset link sent.';
                    forgotSuccessBox.classList.remove('hidden');
                }
            } catch (errorData) {
                const box = document.getElementById('auth-forgot-errors');
                if (box) {
                    box.textContent = formatErrors(errorData.errors);
                    box.classList.remove('hidden');
                }
            }
        });

        showAuthPanel('login');

        const queryPanel = new URLSearchParams(window.location.search).get('auth');
        if (queryPanel && ['login', 'register', 'forgot'].includes(queryPanel)) {
            showAuthPanel(queryPanel);
            openModal('auth-modal');
        }

        @if (session('status') === 'profile-updated')
            showAlert('Profile updated', 'Your profile was saved successfully.', 'success');
        @elseif (session('status') === 'password-updated')
            showAlert('Password updated', 'Your password was changed successfully.', 'success');
        @endif
    </script>
    @stack('scripts')
</body>
</html>
