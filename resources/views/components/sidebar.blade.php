<aside id="app-sidebar"
       class="fixed md:static z-40 inset-y-0 left-0 w-72 shrink-0 transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-out
              bg-ink-900/80 backdrop-blur-xl border-r border-white/10 flex flex-col">
    <div class="flex items-center gap-2 px-5 py-4 border-b border-white/10">
       <div class="h-9 w-9 rounded-xl overflow-hidden shadow-glow">
    <img src="/logo.png" alt="ABX GPT Logo" class="h-full w-full object-cover">
</div>
        <div>
            <div class="text-sm font-bold text-white tracking-tight">ABX GPT</div>
            <div class="text-[11px] text-slate-400">Free · v1.0</div>
        </div>
    </div>

    <div class="px-4 pt-4">
        <button onclick="startNewChat()" class="w-full btn-primary justify-center">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
            New chat
        </button>
        <div class="mt-3 relative">
            <input id="chat-search-input" type="text" placeholder="Search chats..."
                class="w-full bg-ink-800/70 border border-white/10 rounded-xl pl-9 pr-3 py-2 text-sm placeholder:text-slate-500 ring-focus" />
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
        </div>
    </div>

   <nav class="flex-1 overflow-y-auto px-3 py-4">

    <div class="px-2 text-[11px] uppercase tracking-wider text-slate-500 font-semibold mb-3">
        Recent Chats
    </div>

    <ul id="recent-chats-list" class="space-y-1">

        @forelse($chats as $chat)

            <li id="chat-item-{{ $chat->id }}" class="relative group/chat-item">

                <button
                    onclick="loadChat({{ $chat->id }})"
                    class="w-full text-left group flex items-center gap-2 rounded-xl pl-3 pr-10 py-2 text-sm transition text-slate-300 hover:bg-white/5 hover:text-white"
                >

                    <svg class="h-4 w-4 text-slate-400 group-hover:text-brand-300"
                         viewBox="0 0 24 24"
                         fill="none"
                         stroke="currentColor"
                         stroke-width="2">

                        <path d="M21 15a4 4 0 0 1-4 4H8l-5 4V6a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4z"/>

                    </svg>

                    <span id="chat-title-{{ $chat->id }}" class="truncate flex-1">
                        {{ $chat->title }}
                    </span>

                </button>

                <button type="button"
                        class="absolute right-1.5 top-1.5 btn-ghost !px-2 opacity-0 group-hover/chat-item:opacity-100 focus:opacity-100"
                        data-chat-menu-toggle="{{ $chat->id }}">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="5" cy="12" r="1.5"/><circle cx="12" cy="12" r="1.5"/><circle cx="19" cy="12" r="1.5"/>
                    </svg>
                </button>

                <div id="chat-menu-{{ $chat->id }}" class="hidden absolute right-1 top-10 z-20 card p-1 min-w-32">
                    <button type="button" class="w-full text-left btn-ghost justify-start" data-chat-rename="{{ $chat->id }}">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/></svg>
                        Rename
                    </button>
                    <button type="button" class="w-full text-left btn-ghost justify-start text-rose-300 hover:text-rose-200" data-chat-delete="{{ $chat->id }}">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                        Delete
                    </button>
                </div>

            </li>

        @empty

            <li id="no-chats-placeholder" class="text-slate-500 text-sm px-3 py-2">
                No chats yet
            </li>

        @endforelse

    </ul>

</nav>

    <div class="border-t border-white/10 p-3">
        <div class="rounded-xl bg-gradient-to-br from-brand-500/15 to-fuchsia-500/10 border border-white/10 p-3 mb-3">
            <div class="text-xs font-semibold text-white">Welcome to ABX GPT</div>
            <p class="text-[11px] text-slate-300 mt-1">Free access to smart chat with no paywall.</p>
        </div>

        <div class="relative">
            <button id="profile-btn" class="w-full flex items-center gap-3 rounded-xl px-2 py-2 hover:bg-white/5 transition">
                @php
                    $user = auth()->user();
                    $avatarUrl = $user && $user->profile_photo_path ? Storage::url($user->profile_photo_path) : null;
                    $initials = 'G';
                    if ($user) {
                        $parts = preg_split('/\s+/', trim($user->name));
                        $initials = strtoupper(
                            substr($parts[0] ?? '', 0, 1) .
                            substr($parts[1] ?? '', 0, 1)
                        );
                    }
                @endphp
                <div id="sidebar-avatar" class="h-9 w-9 rounded-full overflow-hidden {{ $avatarUrl ? 'bg-transparent' : 'bg-gradient-to-br from-emerald-400 to-brand-500 grid place-items-center text-white text-sm font-bold' }}">
                    @if($avatarUrl)
                        <img src="{{ $avatarUrl }}" alt="Profile photo" class="h-full w-full object-cover" />
                    @else
                        {{ $initials ?: 'U' }}
                    @endif
                </div>
                <div class="flex-1 text-left">
                    <div id="sidebar-user-name" class="text-sm font-medium text-white">{{ auth()->check() ? auth()->user()->name : 'Guest user' }}</div>
                    <div id="sidebar-user-email" class="text-[11px] text-slate-400">{{ auth()->check() ? auth()->user()->email : 'Free plan' }}</div>
                </div>
                <svg class="h-4 w-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
            </button>
            <div id="profile-menu" class="hidden absolute bottom-14 left-0 right-0 card p-1 animate-pop-in">
                <div class="px-3 py-2 border-b border-white/10">
                    <p class="text-xs text-slate-400">Account</p>
                    <p id="profile-menu-name" class="text-sm text-white font-medium truncate">{{ auth()->check() ? auth()->user()->name : 'Guest user' }}</p>
                    <p id="profile-menu-email" class="text-xs text-slate-400 truncate">{{ auth()->check() ? auth()->user()->email : 'Sign in to save chats permanently' }}</p>
                </div>
                <button class="w-full text-left btn-ghost" data-open-modal="settings-modal">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 0 0 .3 1.8l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.7 1.7 0 0 0-1.8-.3 1.7 1.7 0 0 0-1 1.5V21a2 2 0 1 1-4 0v-.1a1.7 1.7 0 0 0-1-1.5"/></svg> Settings
                </button>
                <button class="w-full text-left btn-ghost" data-theme-toggle>
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8z"/></svg> Toggle theme
                </button>
                @guest
                    <button class="w-full text-left btn-ghost" data-open-modal="auth-modal" data-auth-view="login">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 17l5-5-5-5M21 12H9M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/></svg> Sign in
                    </button>
                @else
                    <form id="logout-form" method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="button" class="w-full text-left btn-ghost" data-open-modal="confirm-logout-modal">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg> Logout
                        </button>
                    </form>
                @endguest
            </div>
        </div>
    </div>
</aside>
<div id="sidebar-backdrop" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-30 md:hidden"></div>

{{-- Delete confirmation modal --}}
<div id="confirm-delete-modal" class="hidden fixed inset-0 z-50 grid place-items-center bg-black/60 backdrop-blur-md p-4 transition-opacity duration-200 opacity-0">
    <div class="card max-w-sm w-full p-6 animate-pop-in">
        <div class="h-12 w-12 grid place-items-center rounded-full bg-rose-500/15 text-rose-400 mb-3">
            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 9v4m0 4h.01M10.3 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/></svg>
        </div>
        <h3 class="text-lg font-semibold text-white">Delete this chat?</h3>
        <p class="text-sm text-slate-400 mt-1">This will permanently remove the conversation. This action cannot be undone.</p>
        <div class="mt-5 flex justify-end gap-2">
            <button class="btn-ghost" data-close-modal="confirm-delete-modal">Cancel</button>
            <button id="confirm-delete-chat-btn" class="btn-primary !bg-gradient-to-br !from-rose-500 !to-rose-700">Delete</button>
        </div>
    </div>
</div>

{{-- Rename chat modal --}}
<div id="rename-chat-modal" class="hidden fixed inset-0 z-50 grid place-items-center bg-black/60 backdrop-blur-md p-4 transition-opacity duration-200 opacity-0">
    <div class="card max-w-md w-full p-6 animate-pop-in">
        <div class="h-12 w-12 grid place-items-center rounded-full bg-brand-500/15 text-brand-300 mb-3">
            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/></svg>
        </div>
        <h3 class="text-lg font-semibold text-white">Rename chat</h3>
        <p class="text-sm text-slate-400 mt-1">Update this conversation title.</p>

        <form id="rename-chat-form" class="mt-4 space-y-3">
            <input id="rename-chat-input"
                   type="text"
                   maxlength="120"
                   placeholder="Enter new chat title"
                   class="w-full bg-ink-800/70 border border-white/10 rounded-xl px-3 py-2.5 text-sm text-white placeholder:text-slate-500 ring-focus" />
            <p id="rename-chat-error" class="hidden text-xs text-rose-300"></p>
            <div class="flex justify-end gap-2">
                <button type="button" class="btn-ghost" data-close-modal="rename-chat-modal">Cancel</button>
                <button type="submit" class="btn-primary !px-5">Save</button>
            </div>
        </form>
    </div>
</div>

{{-- Logout confirmation modal --}}
<div id="confirm-logout-modal" class="hidden fixed inset-0 z-50 grid place-items-center bg-black/60 backdrop-blur-md p-4 transition-opacity duration-200 opacity-0">
    <div class="card max-w-sm w-full p-6 animate-pop-in">
        <div class="h-12 w-12 grid place-items-center rounded-full bg-amber-500/15 text-amber-300 mb-3">
            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
        </div>
        <h3 class="text-lg font-semibold text-white">Logout from ABX GPT?</h3>
        <p class="text-sm text-slate-400 mt-1">You can sign in again anytime to continue your saved conversations.</p>
        <div class="mt-5 flex justify-end gap-2">
            <button class="btn-ghost" data-close-modal="confirm-logout-modal">No</button>
            <button class="btn-primary !px-5" onclick="document.getElementById('logout-form')?.submit()">Yes</button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
    const recentChatsList = document.getElementById('recent-chats-list');
    const chatSearchInput = document.getElementById('chat-search-input');
    const confirmDeleteBtn = document.getElementById('confirm-delete-chat-btn');
    const renameChatForm = document.getElementById('rename-chat-form');
    const renameChatInput = document.getElementById('rename-chat-input');
    const renameChatError = document.getElementById('rename-chat-error');
    let deleteChatId = null;
    let renameChatId = null;

    function closeAllChatMenus() {
        document.querySelectorAll('[id^="chat-menu-"]').forEach((menu) => menu.classList.add('hidden'));
    }

    function ensurePlaceholder() {
        if (!recentChatsList) return;
        const visibleChatItems = recentChatsList.querySelectorAll('li[id^="chat-item-"]:not(.hidden)');
        let placeholder = document.getElementById('no-chats-placeholder');

        if (visibleChatItems.length === 0 && !placeholder) {
            placeholder = document.createElement('li');
            placeholder.id = 'no-chats-placeholder';
            placeholder.className = 'text-slate-500 text-sm px-3 py-2';
            placeholder.textContent = chatSearchInput?.value.trim() ? 'No chats match your search.' : 'No chats yet';
            recentChatsList.appendChild(placeholder);
            return;
        }

        if (visibleChatItems.length === 0 && placeholder) {
            placeholder.textContent = chatSearchInput?.value.trim() ? 'No chats match your search.' : 'No chats yet';
            return;
        }

        if (visibleChatItems.length > 0 && placeholder) {
            placeholder.remove();
        }
    }

    function filterChatList() {
        if (!recentChatsList || !chatSearchInput) return;
        const query = chatSearchInput.value.trim().toLowerCase();
        recentChatsList.querySelectorAll('li[id^="chat-item-"]').forEach((item) => {
            const title = item.querySelector('[id^="chat-title-"]')?.textContent?.trim().toLowerCase() || '';
            if (!query || title.includes(query)) {
                item.classList.remove('hidden');
            } else {
                item.classList.add('hidden');
                const menu = item.querySelector('[id^="chat-menu-"]');
                if (menu) menu.classList.add('hidden');
            }
        });
        ensurePlaceholder();
    }

    document.addEventListener('click', (event) => {
        const toggleBtn = event.target.closest('[data-chat-menu-toggle]');
        const renameBtn = event.target.closest('[data-chat-rename]');
        const deleteBtn = event.target.closest('[data-chat-delete]');

        if (toggleBtn) {
            event.stopPropagation();
            const chatId = toggleBtn.dataset.chatMenuToggle;
            const menu = document.getElementById(`chat-menu-${chatId}`);
            const willOpen = menu?.classList.contains('hidden');
            closeAllChatMenus();
            if (menu && willOpen) {
                menu.classList.remove('hidden');
            }
            return;
        }

        if (renameBtn) {
            event.stopPropagation();
            const chatId = renameBtn.dataset.chatRename;
            const titleNode = document.getElementById(`chat-title-${chatId}`);
            const currentTitle = titleNode?.textContent?.trim() || '';
            closeAllChatMenus();
            renameChatId = chatId;
            if (renameChatInput) renameChatInput.value = currentTitle;
            if (renameChatError) {
                renameChatError.classList.add('hidden');
                renameChatError.textContent = '';
            }
            openModal('rename-chat-modal');
            window.setTimeout(() => renameChatInput?.focus(), 80);

            return;
        }

        if (deleteBtn) {
            event.stopPropagation();
            deleteChatId = deleteBtn.dataset.chatDelete;
            closeAllChatMenus();
            openModal('confirm-delete-modal');
            return;
        }

        if (!event.target.closest('[id^="chat-menu-"]')) {
            closeAllChatMenus();
        }
    });

    chatSearchInput?.addEventListener('input', filterChatList);

    confirmDeleteBtn?.addEventListener('click', () => {
        if (!deleteChatId) return;

        fetch(`/chat/${deleteChatId}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrf,
            },
        })
        .then((res) => res.json().then((data) => ({ ok: res.ok, data })))
        .then(({ ok, data }) => {
            if (!ok || !data.success) throw new Error('Delete failed');

            document.getElementById(`chat-item-${deleteChatId}`)?.remove();
            if (window.currentChatId && Number(window.currentChatId) === Number(deleteChatId)) {
                if (typeof window.startNewChat === 'function') {
                    window.startNewChat();
                } else {
                    window.currentChatId = null;
                }
            }

            ensurePlaceholder();
            showToast('Chat deleted');
            closeModal('confirm-delete-modal');
            deleteChatId = null;
        })
        .catch(() => {
            showToast('Unable to delete chat');
            closeModal('confirm-delete-modal');
            deleteChatId = null;
        });
    });

    renameChatForm?.addEventListener('submit', (event) => {
        event.preventDefault();
        if (!renameChatId) return;

        const trimmedTitle = renameChatInput?.value?.trim() || '';
        if (!trimmedTitle) {
            if (renameChatError) {
                renameChatError.textContent = 'Title cannot be empty.';
                renameChatError.classList.remove('hidden');
            }
            return;
        }

        fetch(`/chat/${renameChatId}`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrf,
            },
            body: JSON.stringify({ title: trimmedTitle }),
        })
        .then((res) => res.json().then((data) => ({ ok: res.ok, data })))
        .then(({ ok, data }) => {
            if (!ok || !data.success) throw new Error(data?.message || 'Rename failed');
            const titleNode = document.getElementById(`chat-title-${renameChatId}`);
            if (titleNode) titleNode.textContent = data.title;
            closeModal('rename-chat-modal');
            showToast('Chat renamed');
            renameChatId = null;
        })
        .catch(() => {
            if (renameChatError) {
                renameChatError.textContent = 'Unable to rename chat. Try again.';
                renameChatError.classList.remove('hidden');
            }
        });
    });
});
</script>
