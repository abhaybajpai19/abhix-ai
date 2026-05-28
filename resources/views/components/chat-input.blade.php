<div class="sticky bottom-0 inset-x-0 bg-gradient-to-t from-ink-950 via-ink-950/95 to-transparent pt-6 pb-4 px-3 md:px-6">
    <div class="max-w-3xl mx-auto">
        <div class="flex justify-center mb-2">
            <button id="scrollToBottomBtn" type="button" class="hidden btn-ghost !px-3 !py-1.5 border border-white/15 rounded-full">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 5v14M19 12l-7 7-7-7"/>
                </svg>
                New messages
            </button>
        </div>

        <form id="chatForm" class="group relative card p-2 md:p-3 focus-within:shadow-glow transition">

            <!-- <div class="mb-3 flex flex-wrap items-center gap-2">
                <button id="modeTextBtn" type="button" class="btn-ghost !px-3 !py-2 rounded-full bg-white/5 text-white">Text</button>
                <button id="modeImageBtn" type="button" class="btn-ghost !px-3 !py-2 rounded-full text-slate-300">Image</button>
            </div> -->

            <!-- <div id="imageUploadGroup" class="hidden mb-3 rounded-2xl border border-white/10 bg-white/5 p-3">
                <label for="imageUploadInput" class="text-xs uppercase tracking-[0.2em] text-slate-500">Upload image</label>
                <input id="imageUploadInput" name="image_file" type="file" accept="image/*" class="mt-2 block w-full text-sm text-slate-200 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-slate-700 file:text-white" />
                <p class="mt-2 text-xs text-slate-400">Optional: attach an image reference for the generation request.</p>
            </div> -->

            <textarea
                id="messageInput"
                rows="1"
                placeholder="Message ABX GPT…"
                oninput="this.style.height='auto'; this.style.height=Math.min(this.scrollHeight,220)+'px'"
                class="w-full bg-transparent resize-none px-3 pt-2 pb-12 text-[15px] leading-6 placeholder:text-slate-500 focus:outline-none max-h-56"
            ></textarea>

            <div class="absolute bottom-2 left-2 right-2 flex items-center gap-1">

                <div class="flex-1"></div>

                <button id="stopButton" type="button" class="hidden btn-ghost !px-3 !py-2 border border-white/15">
                    Stop
                </button>
                <button id="sendButton" type="submit" class="btn-primary !px-3 !py-2">
                    Send
                </button>

            </div>
        </form>

        <p class="text-center text-[11px] text-slate-500 mt-3">
            ABX GPT may produce inaccurate information.
        </p>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {

    window.currentChatId = null;
    window.isTemporaryChat = false;
    window.ephemeralHistory = [];

    const form = document.getElementById('chatForm');
    const temporaryChatBanner = document.getElementById('temporary-chat-banner');
    const temporaryChatBtn = document.getElementById('temporary-chat-btn');
    const input = document.getElementById('messageInput');
    const messagesContainer = document.getElementById('messagesContainer');
    const chatScrollContainer = document.getElementById('chatMessages');
    const stopButton = document.getElementById('stopButton');
    const sendButton = document.getElementById('sendButton');
    const scrollToBottomBtn = document.getElementById('scrollToBottomBtn');
    const modeTextBtn = document.getElementById('modeTextBtn');
    const modeImageBtn = document.getElementById('modeImageBtn');
    const imageUploadGroup = document.getElementById('imageUploadGroup');
    const imageUploadInput = document.getElementById('imageUploadInput');
    let activeController = null;
    let stopRequested = false;
    let isGenerating = false;
    let currentMode = 'text';

    function scrollToBottom() {
        if (!chatScrollContainer) return;
        chatScrollContainer.scrollTo({
            top: chatScrollContainer.scrollHeight,
            behavior: 'smooth',
        });
    }

    function isNearBottom() {
        if (!chatScrollContainer) return true;
        const distance = chatScrollContainer.scrollHeight - (chatScrollContainer.scrollTop + chatScrollContainer.clientHeight);
        return distance < 100;
    }

    function toggleScrollToBottomButton() {
        if (!scrollToBottomBtn || !chatScrollContainer) return;
        scrollToBottomBtn.classList.toggle('hidden', isNearBottom());
    }

    function setGeneratingState(generating) {
        isGenerating = generating;
        if (generating) {
            sendButton?.classList.add('hidden');
            stopButton?.classList.remove('hidden');
        } else {
            stopButton?.classList.add('hidden');
            sendButton?.classList.remove('hidden');
        }
    }

    function decreaseGuestRemainingCount() {
        const guestCount = document.getElementById('guest-remaining-count');
        if (!guestCount) return;
        const current = parseInt(guestCount.textContent || '0', 10);
        guestCount.textContent = String(Math.max(0, current - 1));
    }

    function setTemporaryChatUi(active) {
        window.isTemporaryChat = active;
        temporaryChatBanner?.classList.toggle('hidden', !active);
        temporaryChatBtn?.classList.toggle('!border-violet-400/50', active);
        temporaryChatBtn?.classList.toggle('!bg-violet-500/15', active);
        temporaryChatBtn?.classList.toggle('!text-violet-100', active);
    }

    function pushEphemeralExchange(userMessage, assistantMessage) {
        window.ephemeralHistory.push({ role: 'user', content: userMessage });
        window.ephemeralHistory.push({ role: 'assistant', content: assistantMessage });
    }

    function appendGuestChatToSidebar(chatId, title) {
        if (!String(chatId).startsWith('guest-')) return;
        const list = document.getElementById('recent-chats-list');
        if (!list || document.getElementById(`chat-item-${chatId}`)) return;

        document.getElementById('no-chats-placeholder')?.remove();

        const li = document.createElement('li');
        li.id = `chat-item-${chatId}`;
        li.className = 'relative group/chat-item';
        li.innerHTML = `
            <button onclick="loadChat('${chatId}')" class="w-full text-left group flex items-center gap-2 rounded-xl pl-3 pr-10 py-2 text-sm transition text-slate-300 hover:bg-white/5 hover:text-white">
                <svg class="h-4 w-4 text-slate-400 group-hover:text-brand-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a4 4 0 0 1-4 4H8l-5 4V6a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4z"/></svg>
                <span id="chat-title-${chatId}" class="truncate flex-1">${title}</span>
            </button>
        `;
        list.prepend(li);
    }

    window.startTemporaryChat = function() {
        window.currentChatId = null;
        window.ephemeralHistory = [];
        setTemporaryChatUi(true);
        renderNewChatGreeting();
    };

    window.exitTemporaryChat = function() {
        setTemporaryChatUi(false);
        window.startNewChat();
    };

    function setMode(mode) {
        currentMode = mode;
        if (mode === 'image') {
            imageUploadGroup?.classList.remove('hidden');
            modeImageBtn?.classList.add('bg-white/10', 'text-white');
            modeTextBtn?.classList.remove('bg-white/10', 'text-white');
            input.placeholder = 'Describe the image you want to generate...';
        } else {
            imageUploadGroup?.classList.add('hidden');
            modeTextBtn?.classList.add('bg-white/10', 'text-white');
            modeImageBtn?.classList.remove('bg-white/10', 'text-white');
            input.placeholder = 'Message ABX GPT…';
        }
    }

    async function readFileAsDataUrl(file) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.onload = () => resolve(reader.result);
            reader.onerror = reject;
            reader.readAsDataURL(file);
        });
    }

    async function typeText(element, text, speed = 15, keepPinned = true) {

        element.innerHTML = '';

        for (let i = 0; i < text.length; i++) {
            if (stopRequested) {
                break;
            }

            element.innerHTML += text.charAt(i);

            if (keepPinned && isNearBottom()) {
                scrollToBottom();
            } else {
                toggleScrollToBottomButton();
            }

            await new Promise(resolve => setTimeout(resolve, speed));
        }
    }

    async function fetchNewGreeting() {
        try {
            const response = await fetch('/chat/greeting/new', {
                headers: {
                    'Accept': 'application/json',
                },
            });

            if (!response.ok) {
                throw new Error('Failed to load greeting');
            }

            const data = await response.json();
            if (!data.success || !data.greeting) {
                throw new Error('Invalid greeting response');
            }

            return data.greeting;
        } catch (error) {
            return 'How can I help you today?';
        }
    }

    async function renderNewChatGreeting() {
        const greeting = await fetchNewGreeting();
        const bubbleId = 'greet-' + Date.now();

        messagesContainer.innerHTML = `
            <div class="flex justify-center items-start pt-8 md:pt-12 mb-4">
                <div id="${bubbleId}" class="bg-ink-800/90 text-white px-5 py-3 rounded-2xl max-w-xl whitespace-pre-wrap shadow-lg text-center"></div>
            </div>
        `;

        const bubble = document.getElementById(bubbleId);
        if (bubble) {
            typeText(bubble, greeting, 12, true);
        }
    }

    // START NEW CHAT
    window.startNewChat = function() {
        setTemporaryChatUi(false);
        window.currentChatId = null;
        window.ephemeralHistory = [];
        renderNewChatGreeting();
    }

    // LOAD OLD CHAT
    window.loadChat = async function(chatId) {

        try {

            const response = await fetch('/chat/' + encodeURIComponent(chatId));

            const data = await response.json();

            setTemporaryChatUi(false);
            window.currentChatId = chatId;
            window.ephemeralHistory = [];

            messagesContainer.innerHTML = '';

            data.chat.messages.forEach(msg => {
                const isImageMessage = typeof msg.message === 'string' && msg.message.startsWith('__IMAGE__:');
                const messageContent = isImageMessage ? msg.message.replace('__IMAGE__:', '') : msg.message;

                if (msg.role === 'user') {
                    messagesContainer.innerHTML += `
                        <div class="flex justify-end mb-4">
                            <div class="inline-block bg-brand-500 text-white px-4 py-3 rounded-2xl max-w-[75%] whitespace-pre-wrap break-words shadow-lg">
                                ${messageContent}
                            </div>
                        </div>
                    `;
                } else if (isImageMessage) {
                    messagesContainer.innerHTML += `
                        <div class="flex justify-start mb-4">
                            <div class="inline-block bg-ink-800 rounded-2xl max-w-[75%] overflow-hidden shadow-lg">
                                <img src="${messageContent}" alt="Generated image" class="w-full h-auto object-cover" />
                            </div>
                        </div>
                    `;
                } else {
                    messagesContainer.innerHTML += `
                        <div class="flex justify-start mb-4">
                            <div class="inline-block bg-ink-800 text-white px-4 py-3 rounded-2xl max-w-[75%] whitespace-pre-wrap break-words shadow-lg">
                                ${messageContent}
                            </div>
                        </div>
                    `;
                }
            });

            scrollToBottom();

        } catch (error) {

            console.error(error);
        }
    }

    // SEND MESSAGE
    form.addEventListener('submit', async (e) => {

        e.preventDefault();

        if (isGenerating) {
            return;
        }

        const message = input.value.trim();
        const imageFile = imageUploadInput?.files?.[0] ?? null;

        if (currentMode === 'image' && !message && !imageFile) return;
        if (currentMode === 'text' && !message) return;

        // USER MESSAGE
        if (currentMode === 'image' && imageFile) {
            const imageUrl = await readFileAsDataUrl(imageFile);
            messagesContainer.innerHTML += `
                <div class="flex justify-end mb-4">
                    <div class="inline-block bg-brand-500 text-white rounded-2xl max-w-[75%] overflow-hidden shadow-lg">
                        <img src="${imageUrl}" alt="Uploaded image" class="w-full h-auto object-cover" />
                        ${message ? `<div class="px-4 py-3 text-sm text-white">${message}</div>` : ''}
                    </div>
                </div>
            `;
        } else {
            messagesContainer.innerHTML += `
                <div class="flex justify-end mb-4">
                    <div class="inline-block bg-brand-500 text-white px-4 py-3 rounded-2xl max-w-[75%] whitespace-pre-wrap break-words shadow-lg">
                        ${message}
                    </div>
                </div>
            `;
        }

        input.value = '';
        input.style.height = 'auto';
        if (imageUploadInput) imageUploadInput.value = '';

        scrollToBottom();

        // THINKING MESSAGE
        const loadingId = 'loading-' + Date.now();

        messagesContainer.innerHTML += `
            <div id="${loadingId}" class="flex justify-start mb-4">
                <div class="inline-block bg-ink-800 text-white px-4 py-3 rounded-2xl max-w-[75%] whitespace-pre-wrap break-words animate-pulse">
                    ABX GPT is thinking...
                </div>
            </div>
        `;

        scrollToBottom();
        stopRequested = false;
        activeController = new AbortController();
        setGeneratingState(true);

        try {
            const endpoint = currentMode === 'image' ? '/chat/image' : '/chat';
            const options = {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                signal: activeController.signal,
            };

            if (currentMode === 'image') {
                const formData = new FormData();
                formData.append('prompt', message);
                if (window.currentChatId) {
                    formData.append('chat_id', window.currentChatId);
                }
                if (imageFile) {
                    formData.append('image_file', imageFile);
                }
                options.body = formData;
            } else {
                const payload = {
                    message: message,
                };

                if (window.isTemporaryChat) {
                    payload.ephemeral = true;
                    payload.history = window.ephemeralHistory;
                } else if (window.currentChatId) {
                    payload.chat_id = window.currentChatId;
                }

                options.headers['Content-Type'] = 'application/json';
                options.body = JSON.stringify(payload);
            }

            const response = await fetch(endpoint, options);
            const data = await response.json();

            // REMOVE THINKING
            document.getElementById(loadingId)?.remove();

            // GUEST LIMIT
            if (data.limit_reached) {
                if (typeof openModal === 'function') {
                    openModal('guest-limit-modal');
                }
                return;
            }

            if (!response.ok || !data.success) {
                throw new Error(data.response || 'Failed to get response.');
            }

            if (!window.isTemporaryChat) {
                decreaseGuestRemainingCount();
            }

            // SAVE CHAT ID
            if (data.chat_id && !window.isTemporaryChat) {
                const isNewGuestChat = String(data.chat_id).startsWith('guest-') && !window.currentChatId;
                window.currentChatId = data.chat_id;
                if (isNewGuestChat && data.title) {
                    appendGuestChatToSidebar(data.chat_id, data.title);
                }
            }

            if (currentMode === 'image' && data.image_url) {
                messagesContainer.innerHTML += `
                    <div class="flex justify-start mb-4">
                        <div class="inline-block bg-ink-800 rounded-2xl max-w-[75%] overflow-hidden shadow-lg">
                            <img src="${data.image_url}" alt="Generated image" class="w-full h-auto object-cover" />
                        </div>
                    </div>
                `;
                scrollToBottom();
            } else {
                // AI MESSAGE CONTAINER
                const aiMessageId = 'ai-' + Date.now();

                messagesContainer.innerHTML += `
                    <div class="flex justify-start mb-4">
                        <div id="${aiMessageId}" class="inline-block bg-ink-800 text-white px-4 py-3 rounded-2xl max-w-[75%] whitespace-pre-wrap break-words shadow-lg"></div>
                    </div>
                `;

                if (isNearBottom()) {
                    scrollToBottom();
                } else {
                    toggleScrollToBottomButton();
                }

                // TYPING EFFECT
                const aiElement = document.getElementById(aiMessageId);
                const shouldPinDuringTyping = isNearBottom();
                await typeText(aiElement, data.response, 15, shouldPinDuringTyping);
                if (window.isTemporaryChat) {
                    pushEphemeralExchange(message, data.response);
                }
                if (stopRequested) {
                    showToast('Response stopped');
                }
            }

        } catch (error) {
            if (error.name === 'AbortError') {
                document.getElementById(loadingId)?.remove();
                showToast('Response stopped');
                return;
            }

            document.getElementById(loadingId)?.remove();

            messagesContainer.innerHTML += `
                <div class="flex justify-start mb-4">
                    <div class="inline-block bg-red-500/20 text-red-300 px-4 py-3 rounded-2xl max-w-[75%] whitespace-pre-wrap break-words">
                        Error getting response.
                    </div>
                </div>
            `;

            scrollToBottom();
        } finally {
            activeController = null;
            stopRequested = false;
            setGeneratingState(false);
        }
    });

    const isSendOnEnterEnabled = () => localStorage.getItem('nova-send-on-enter') !== 'false';

    input.addEventListener('keydown', (event) => {
        if (!isSendOnEnterEnabled()) {
            return;
        }

        if (event.key === 'Enter' && !event.shiftKey) {
            event.preventDefault();
            form.requestSubmit();
        }
    });

    stopButton?.addEventListener('click', () => {
        if (!isGenerating) return;
        stopRequested = true;
        if (activeController) {
            activeController.abort();
        }
    });

    modeTextBtn?.addEventListener('click', () => setMode('text'));
    modeImageBtn?.addEventListener('click', () => setMode('image'));
    setMode('text');

    if (!messagesContainer.innerHTML.trim()) {
        renderNewChatGreeting();
    }

    chatScrollContainer?.addEventListener('scroll', toggleScrollToBottomButton);
    scrollToBottomBtn?.addEventListener('click', () => {
        scrollToBottom();
        toggleScrollToBottomButton();
    });
    window.setTimeout(toggleScrollToBottomButton, 50);
});
</script>