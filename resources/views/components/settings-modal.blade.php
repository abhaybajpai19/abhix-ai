<div id="settings-modal" class="hidden fixed inset-0 z-50 grid place-items-center bg-black/70 backdrop-blur-md p-4 transition-opacity duration-200 opacity-0">
    <div class="card w-full max-w-2xl p-0 overflow-hidden animate-pop-in">
        <div class="flex items-center justify-between px-6 py-4 border-b border-white/10">
            <h3 class="text-lg font-semibold text-white">Settings</h3>
            <button class="btn-ghost !px-2" data-close-modal="settings-modal">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-[160px_1fr]">
            <div class="border-r border-white/10 p-3 space-y-1">
                @php $settingsTabs = ['General' => 'general', 'Appearance' => 'appearance', 'Account' => 'account']; @endphp
                @foreach($settingsTabs as $label => $key)
                    <button type="button" class="w-full text-left btn-ghost settings-tab {{ $loop->first ? '!bg-white/5 !text-white' : '' }}" data-settings-tab="{{ $key }}">{{ $label }}</button>
                @endforeach
            </div>
            <div class="p-6 space-y-5">
                <div class="settings-panel" data-settings-panel="general">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-sm font-medium text-white">Send on Enter</div>
                            <div class="text-xs text-slate-400">Press Enter to send messages</div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input id="send-on-enter-checkbox" type="checkbox" class="sr-only peer">
                            <div class="w-11 h-6 bg-ink-700 rounded-full peer-checked:bg-brand-500 transition relative after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-5 after:w-5 peer-checked:after:translate-x-5 after:transition"></div>
                        </label>
                    </div>
                    <div class="border-t border-white/10 pt-4 flex items-center justify-between">
                        <div>
                            <div class="text-sm font-medium text-rose-300">Delete all chats</div>
                            <div class="text-xs text-slate-400">Permanently remove all conversations</div>
                        </div>
                        <button id="delete-all-chats-btn" type="button" class="btn-ghost !text-rose-300 hover:!bg-rose-500/10">Delete</button>
                    </div>
                </div>
                <div class="settings-panel hidden" data-settings-panel="appearance">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-sm font-medium text-white">Theme</div>
                            <div class="text-xs text-slate-400">Choose dark or light appearance</div>
                        </div>
                        <button data-theme-toggle class="btn-ghost !border !border-white/10">Toggle</button>
                    </div>
                    <div class="border-t border-white/10 pt-4">
                        <div class="text-sm font-medium text-white">Appearance tip</div>
                        <div class="text-xs text-slate-400">Theme changes are saved locally and persist on this browser.</div>
                    </div>
                </div>
                <div class="settings-panel hidden" data-settings-panel="account">
                    @auth
                        @php $user = auth()->user(); @endphp
                        <div class="space-y-4">
                            <div class="flex items-center gap-3">
                                <div class="h-14 w-14 rounded-full bg-slate-700 overflow-hidden ring-2 ring-white/15 relative">
                                    <img id="profile-photo-preview" src="{{ $user->profile_photo_path ? Storage::url($user->profile_photo_path) : '' }}" alt="Profile photo" class="h-full w-full object-cover {{ $user->profile_photo_path ? '' : 'hidden' }}" />
                                    <div id="profile-photo-initials" class="h-full w-full grid place-items-center text-white text-lg font-semibold {{ $user->profile_photo_path ? 'hidden' : '' }}">
                                        {{ collect(explode(' ', $user->name))->map(fn($part) => strtoupper(substr($part, 0, 1)))->take(2)->join('') }}
                                    </div>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-white">{{ $user->name }}</div>
                                    <div class="text-xs text-slate-400">{{ $user->email }}</div>
                                </div>
                            </div>

                            <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-4">
                                @csrf
                                @method('patch')

                                <div>
                                    <label class="text-sm text-slate-200 block mb-2" for="profile_photo">Profile picture</label>
                                    <input id="profile_photo" name="profile_photo" type="file" accept="image/*" class="block w-full text-sm text-slate-200 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-slate-700 file:text-white" />
                                    <p id="profile-photo-warning" class="text-xs text-rose-300 mt-2 hidden"></p>
                                    @if($errors->has('profile_photo'))
                                        <p class="text-xs text-rose-300 mt-2">{{ $errors->first('profile_photo') }}</p>
                                    @endif
                                </div>

                                <div>
                                    <label class="text-sm text-slate-200 block mb-2" for="name">Name</label>
                                    <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" class="w-full rounded-xl border border-white/10 bg-ink-900/70 px-3 py-2 text-sm text-white ring-focus" required />
                                </div>

                                <div>
                                    <label class="text-sm text-slate-200 block mb-2" for="email">Email</label>
                                    <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" class="w-full rounded-xl border border-white/10 bg-ink-900/70 px-3 py-2 text-sm text-white ring-focus" required />
                                </div>

                                <div class="flex justify-end">
                                    <button type="submit" class="btn-primary">Save profile</button>
                                </div>
                            </form>

                            <form method="post" action="{{ route('password.update') }}" class="space-y-4">
                                @csrf
                                @method('put')

                                <div>
                                    <label class="text-sm text-slate-200 block mb-2" for="current_password">Current password</label>
                                    <input id="current_password" name="current_password" type="password" class="w-full rounded-xl border border-white/10 bg-ink-900/70 px-3 py-2 text-sm text-white ring-focus" required />
                                </div>

                                <div>
                                    <label class="text-sm text-slate-200 block mb-2" for="password">New password</label>
                                    <input id="password" name="password" type="password" class="w-full rounded-xl border border-white/10 bg-ink-900/70 px-3 py-2 text-sm text-white ring-focus" required />
                                </div>

                                <div>
                                    <label class="text-sm text-slate-200 block mb-2" for="password_confirmation">Confirm password</label>
                                    <input id="password_confirmation" name="password_confirmation" type="password" class="w-full rounded-xl border border-white/10 bg-ink-900/70 px-3 py-2 text-sm text-white ring-focus" required />
                                </div>

                                <div class="flex justify-end">
                                    <button type="submit" class="btn-primary">Change password</button>
                                </div>
                            </form>
                        </div>
                    @else
                        <div class="space-y-4">
                            <p class="text-sm text-slate-400">Sign in to update profile details and passwords.</p>
                            <button type="button" class="btn-primary" data-open-modal="auth-modal" data-auth-view="login">Sign in</button>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const modal = document.getElementById('settings-modal');
        const tabs = modal.querySelectorAll('[data-settings-tab]');
        const panels = modal.querySelectorAll('[data-settings-panel]');
        const deleteAllChatsBtn = document.getElementById('delete-all-chats-btn');
        const sendOnEnterCheckbox = document.getElementById('send-on-enter-checkbox');
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

        function activateTab(key) {
            tabs.forEach((tab) => {
                tab.classList.toggle('!bg-white/5', tab.dataset.settingsTab === key);
                tab.classList.toggle('!text-white', tab.dataset.settingsTab === key);
            });
            panels.forEach((panel) => {
                panel.classList.toggle('hidden', panel.dataset.settingsPanel !== key);
            });
        }

        tabs.forEach((tab) => {
            tab.addEventListener('click', () => activateTab(tab.dataset.settingsTab));
        });

        const savedSendOnEnter = localStorage.getItem('nova-send-on-enter');
        const sendOnEnterEnabled = savedSendOnEnter !== 'false';
        if (sendOnEnterCheckbox) {
            sendOnEnterCheckbox.checked = sendOnEnterEnabled;
            sendOnEnterCheckbox.addEventListener('change', () => {
                localStorage.setItem('nova-send-on-enter', sendOnEnterCheckbox.checked ? 'true' : 'false');
            });
        }

        const profilePhotoInput = modal.querySelector('#profile_photo');
        const profilePhotoPreview = modal.querySelector('#profile-photo-preview');
        const profilePhotoInitials = modal.querySelector('#profile-photo-initials');
        const profilePhotoWarning = modal.querySelector('#profile-photo-warning');

        profilePhotoInput?.addEventListener('change', () => {
            const file = profilePhotoInput.files?.[0];
            if (!file) {
                return;
            }

            const maxBytes = 2048 * 1024;
            if (file.size > maxBytes) {
                if (profilePhotoWarning) {
                    profilePhotoWarning.textContent = 'File is too large. Please select an image under 2MB.';
                    profilePhotoWarning.classList.remove('hidden');
                }
                profilePhotoPreview?.classList.add('hidden');
                return;
            }

            if (profilePhotoWarning) {
                profilePhotoWarning.textContent = '';
                profilePhotoWarning.classList.add('hidden');
            }

            const reader = new FileReader();
            reader.onload = (event) => {
                if (profilePhotoPreview) {
                    profilePhotoPreview.src = event.target.result;
                    profilePhotoPreview.classList.remove('hidden');
                }
                if (profilePhotoInitials) {
                    profilePhotoInitials.classList.add('hidden');
                }
            };
            reader.readAsDataURL(file);
        });

        deleteAllChatsBtn?.addEventListener('click', async () => {
            if (!confirm('Are you sure you want to delete all chats? This cannot be undone.')) {
                return;
            }
            try {
                const response = await fetch('{{ route('chat.clear') }}', {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                });

                if (!response.ok) {
                    throw new Error('Unable to delete chats');
                }

                document.querySelectorAll('[id^="chat-item-"]').forEach((item) => item.remove());
                activateTab('general');
                closeModal('settings-modal');
                showToast('All chats deleted');
            } catch (error) {
                showToast(error.message || 'Could not delete chats');
            }
        });

        activateTab('general');
    });
</script>
@endpush
