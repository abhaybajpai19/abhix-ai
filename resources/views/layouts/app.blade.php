<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ABX GPT — Your Intelligent Assistant')</title>
    <meta name="description" content="ABX GPT — a free AI chat assistant." />
    <link rel="icon" type="image/png" href="/logo.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
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
            background: #f1f5f9 !important;
            color: #0f172a !important;
        }
        html:not(.dark) .card {
            background: rgba(255, 255, 255, 0.85) !important;
            border-color: rgba(15, 23, 42, 0.12) !important;
            color: #0f172a !important;
        }
        html:not(.dark) [class*="bg-ink-"] {
            background-color: rgba(255, 255, 255, 0.82) !important;
        }
        html:not(.dark) [class*="text-slate-"] {
            color: #334155 !important;
        }
        html:not(.dark) .text-white {
            color: #0f172a !important;
        }
        html:not(.dark) .border-white\/10 {
            border-color: rgba(15, 23, 42, 0.12) !important;
        }
        html:not(.dark) .btn-ghost {
            color: #334155 !important;
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

    document.querySelectorAll('[data-toggle="sidebar"]').forEach(b =>
        b.addEventListener('click', () => {
            sidebar.classList.toggle('-translate-x-full');
            backdrop.classList.toggle('hidden');
        })
    );

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

    document.querySelectorAll('[data-open-modal]').forEach(b =>
        b.addEventListener('click', () => openModal(b.dataset.openModal))
    );

    document.querySelectorAll('[data-close-modal]').forEach(b =>
        b.addEventListener('click', () => closeModal(b.dataset.closeModal))
    );

    // ----- Toast -----
    window.showToast = (msg) => {
        const t = document.getElementById('toast');
        t.querySelector('[data-toast-msg]').textContent = msg;

        t.classList.remove('translate-y-10', 'opacity-0', 'pointer-events-none');

        clearTimeout(window.__toastT);

        window.__toastT = setTimeout(() => {
            t.classList.add('translate-y-10', 'opacity-0', 'pointer-events-none');
        }, 2400);
    };

    // ----- Theme toggle -----
    document.querySelectorAll('[data-theme-toggle]').forEach(b =>
        b.addEventListener('click', () => {
            document.documentElement.classList.toggle('dark');

            localStorage.setItem(
                'nova-theme',
                document.documentElement.classList.contains('dark')
                    ? 'dark'
                    : 'light'
            );

            showToast(
                document.documentElement.classList.contains('dark')
                    ? 'Dark mode enabled'
                    : 'Light mode enabled'
            );
        })
    );

    // ----- Profile dropdown -----
    const profileBtn = document.getElementById('profile-btn');
    const profileMenu = document.getElementById('profile-menu');

    profileBtn?.addEventListener('click', (e) => {
        e.stopPropagation();
        profileMenu.classList.toggle('hidden');
    });

    document.addEventListener('click', () => {
        profileMenu?.classList.add('hidden');
    });

    // ----- Auth Disabled -----
    console.log('Auth routes disabled');
</script>
    @stack('scripts')
</body>
</html>
