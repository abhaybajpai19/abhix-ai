<div id="chatMessages" class="h-full overflow-y-auto px-3 md:px-6 py-6">

    <div id="temporary-chat-banner" class="hidden max-w-3xl mx-auto mb-4">
        <div class="flex items-center justify-between gap-3 rounded-xl border border-violet-500/25 bg-violet-500/10 px-4 py-2.5 text-sm text-violet-100">
            <div class="flex items-center gap-2 min-w-0">
                <svg class="h-4 w-4 shrink-0 text-violet-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                </svg>
                <span class="truncate">Temporary chat — messages are not saved to your account or history.</span>
            </div>
            <button type="button" onclick="typeof exitTemporaryChat === 'function' && exitTemporaryChat()" class="shrink-0 text-xs font-medium text-violet-200 hover:text-white underline-offset-2 hover:underline">
                Exit
            </button>
        </div>
    </div>

    <div class="max-w-3xl mx-auto space-y-6" id="messagesContainer">

    </div>

</div>