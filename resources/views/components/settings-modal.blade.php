<div id="settings-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-md p-3 sm:p-4 transition-opacity duration-200 opacity-0">
    <div class="card w-full max-w-md max-h-[85vh] flex flex-col overflow-hidden animate-pop-in">
        <div class="flex items-center justify-between px-4 py-3 border-b border-white/10 shrink-0">
            <h3 class="text-base font-semibold text-white">Settings</h3>
            <button type="button" class="btn-ghost !px-2" data-close-modal="settings-modal" aria-label="Close settings">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="flex gap-1 p-2 border-b border-white/10 shrink-0 overflow-x-auto">
            @php $settingsTabs = ['General' => 'general', 'Appearance' => 'appearance', 'Account' => 'account']; @endphp
            @foreach($settingsTabs as $label => $key)
                <button type="button" class="settings-tab shrink-0 rounded-lg px-3 py-1.5 text-xs font-medium text-slate-400 hover:text-white hover:bg-white/5 {{ $loop->first ? '!bg-white/10 !text-white' : '' }}" data-settings-tab="{{ $key }}">{{ $label }}</button>
            @endforeach
        </div>

        <div class="overflow-y-auto flex-1 p-4 space-y-4">
            <div class="settings-panel" data-settings-panel="general">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <div class="text-sm font-medium text-white">Send on Enter</div>
                        <div class="text-xs text-slate-400">Press Enter to send messages</div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer shrink-0">
                        <input id="send-on-enter-checkbox" type="checkbox" class="sr-only peer">
                        <div class="w-11 h-6 bg-ink-700 rounded-full peer-checked:bg-brand-500 transition relative after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-5 after:w-5 peer-checked:after:translate-x-5 after:transition"></div>
                    </label>
                </div>
                <div class="border-t border-white/10 pt-4 flex items-center justify-between gap-3">
                    <div>
                        <div class="text-sm font-medium text-rose-300">Delete all chats</div>
                        <div class="text-xs text-slate-400">Permanently remove all conversations</div>
                    </div>
                    <button id="delete-all-chats-btn" type="button" class="btn-ghost !text-rose-300 hover:!bg-rose-500/10 shrink-0">Delete</button>
                </div>
            </div>

            <div class="settings-panel hidden" data-settings-panel="appearance">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <div class="text-sm font-medium text-white">Theme</div>
                        <div class="text-xs text-slate-400">Dark or light appearance</div>
                    </div>
                    <button type="button" data-theme-toggle class="btn-ghost !border !border-white/10 shrink-0">Toggle</button>
                </div>
            </div>

            <div class="settings-panel hidden" data-settings-panel="account">
                @auth
                    @php $user = auth()->user(); @endphp
                    <div class="space-y-4">
                        <div class="flex items-center gap-3">
                            <div class="h-12 w-12 rounded-full bg-slate-700 overflow-hidden ring-2 ring-white/15 relative shrink-0">
                                <img id="profile-photo-preview" src="{{ $user->profile_photo_path ? Storage::url($user->profile_photo_path) : '' }}" alt="Profile photo" class="h-full w-full object-cover {{ $user->profile_photo_path ? '' : 'hidden' }}" />
                                <div id="profile-photo-initials" class="h-full w-full grid place-items-center text-white text-sm font-semibold {{ $user->profile_photo_path ? 'hidden' : '' }}">
                                    {{ collect(explode(' ', $user->name))->map(fn ($part) => strtoupper(substr($part, 0, 1)))->take(2)->join('') }}
                                </div>
                            </div>
                            <div class="min-w-0">
                                <div id="settings-display-name" class="text-sm font-medium text-white truncate">{{ $user->name }}</div>
                                <div id="settings-display-email" class="text-xs text-slate-400 truncate">{{ $user->email }}</div>
                            </div>
                        </div>

                        <form id="settings-profile-form" method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-3">
                            @csrf
                            @method('patch')

                            <div>
                                <label class="text-sm text-slate-200 block mb-1.5" for="profile_photo">Profile picture</label>
                                <input id="profile_photo" name="profile_photo" type="file" accept="image/*" class="block w-full text-sm text-slate-200 file:mr-3 file:py-1.5 file:px-3 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-slate-700 file:text-white" />
                                <p id="profile-photo-warning" class="text-xs text-rose-300 mt-1.5 hidden"></p>
                            </div>

                            <div>
                                <label class="text-sm text-slate-200 block mb-1.5" for="settings_name">Name</label>
                                <input id="settings_name" name="name" type="text" value="{{ old('name', $user->name) }}" class="w-full rounded-xl border border-white/10 bg-ink-900/70 px-3 py-2 text-sm text-white ring-focus" required />
                            </div>

                            <div>
                                <label class="text-sm text-slate-200 block mb-1.5" for="settings_email">Email</label>
                                <input id="settings_email" name="email" type="email" value="{{ old('email', $user->email) }}" class="w-full rounded-xl border border-white/10 bg-ink-900/70 px-3 py-2 text-sm text-white ring-focus" required />
                            </div>

                            <div class="flex justify-end pt-1">
                                <button type="submit" id="settings-profile-submit" class="btn-primary !py-2 !px-4">Save profile</button>
                            </div>
                        </form>

                        <div class="border-t border-white/10 pt-4">
                            <p class="text-xs font-medium text-slate-400 mb-3 uppercase tracking-wide">Change password</p>
                            <form id="settings-password-form" method="post" action="{{ route('password.update') }}" class="space-y-3">
                                @csrf
                                @method('put')

                                <div>
                                    <label class="text-sm text-slate-200 block mb-1.5" for="current_password">Current password</label>
                                    <input id="current_password" name="current_password" type="password" class="w-full rounded-xl border border-white/10 bg-ink-900/70 px-3 py-2 text-sm text-white ring-focus" required />
                                </div>

                                <div>
                                    <label class="text-sm text-slate-200 block mb-1.5" for="password">New password</label>
                                    <input id="password" name="password" type="password" class="w-full rounded-xl border border-white/10 bg-ink-900/70 px-3 py-2 text-sm text-white ring-focus" required />
                                </div>

                                <div>
                                    <label class="text-sm text-slate-200 block mb-1.5" for="password_confirmation">Confirm password</label>
                                    <input id="password_confirmation" name="password_confirmation" type="password" class="w-full rounded-xl border border-white/10 bg-ink-900/70 px-3 py-2 text-sm text-white ring-focus" required />
                                </div>

                                <div class="flex justify-end pt-1">
                                    <button type="submit" id="settings-password-submit" class="btn-primary !py-2 !px-4">Change password</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @else
                    <p class="text-sm text-slate-400">Sign in to update profile details and passwords.</p>
                    <button type="button" class="btn-primary mt-3" data-open-modal="auth-modal" data-auth-view="login">Sign in</button>
                @endauth
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const modal = document.getElementById('settings-modal');
        if (!modal) return;

        const tabs = modal.querySelectorAll('[data-settings-tab]');
        const panels = modal.querySelectorAll('[data-settings-panel]');
        const deleteAllChatsBtn = document.getElementById('delete-all-chats-btn');
        const sendOnEnterCheckbox = document.getElementById('send-on-enter-checkbox');
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

        function activateTab(key) {
            tabs.forEach((tab) => {
                const active = tab.dataset.settingsTab === key;
                tab.classList.toggle('!bg-white/10', active);
                tab.classList.toggle('!text-white', active);
                tab.classList.toggle('text-slate-400', !active);
            });
            panels.forEach((panel) => {
                panel.classList.toggle('hidden', panel.dataset.settingsPanel !== key);
            });
        }

        tabs.forEach((tab) => {
            tab.addEventListener('click', () => activateTab(tab.dataset.settingsTab));
        });

        const savedSendOnEnter = localStorage.getItem('nova-send-on-enter');
        if (sendOnEnterCheckbox) {
            sendOnEnterCheckbox.checked = savedSendOnEnter !== 'false';
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
            if (!file) return;

            if (file.size > 2048 * 1024) {
                profilePhotoWarning.textContent = 'Please select an image under 2MB.';
                profilePhotoWarning.classList.remove('hidden');
                return;
            }

            profilePhotoWarning.classList.add('hidden');
            const reader = new FileReader();
            reader.onload = (event) => {
                profilePhotoPreview.src = event.target.result;
                profilePhotoPreview.classList.remove('hidden');
                profilePhotoInitials?.classList.add('hidden');
            };
            reader.readAsDataURL(file);
        });

        const profileForm = document.getElementById('settings-profile-form');
        profileForm?.addEventListener('submit', async (event) => {
            event.preventDefault();
            const submitBtn = document.getElementById('settings-profile-submit');
            submitBtn.disabled = true;

            try {
                const response = await fetch(profileForm.action, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: new FormData(profileForm),
                });

                const data = await response.json();
                if (!response.ok) {
                    const message = data.errors
                        ? Object.values(data.errors).flat().join(' ')
                        : (data.message || 'Could not update profile.');
                    throw new Error(message);
                }

                if (typeof window.updateProfileUi === 'function') {
                    window.updateProfileUi(data.user);
                }

                closeModal('settings-modal');
                window.showAlert?.('Profile updated', data.message || 'Your profile was saved successfully.', 'success');
            } catch (error) {
                window.showAlert?.('Update failed', error.message || 'Could not update profile.', 'error');
            } finally {
                submitBtn.disabled = false;
            }
        });

        const passwordForm = document.getElementById('settings-password-form');
        passwordForm?.addEventListener('submit', async (event) => {
            event.preventDefault();
            const submitBtn = document.getElementById('settings-password-submit');
            submitBtn.disabled = true;

            try {
                const formData = new FormData();
                formData.append('_method', 'PUT');
                formData.append('current_password', passwordForm.current_password.value);
                formData.append('password', passwordForm.password.value);
                formData.append('password_confirmation', passwordForm.password_confirmation.value);

                const response = await fetch(passwordForm.action, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: formData,
                });

                const data = await response.json();
                if (!response.ok) {
                    const message = data.errors
                        ? Object.values(data.errors).flat().join(' ')
                        : (data.message || 'Could not update password.');
                    throw new Error(message);
                }

                passwordForm.reset();
                closeModal('settings-modal');
                window.showAlert?.('Password updated', data.message || 'Your password was changed successfully.', 'success');
            } catch (error) {
                window.showAlert?.('Update failed', error.message || 'Could not update password.', 'error');
            } finally {
                submitBtn.disabled = false;
            }
        });

        deleteAllChatsBtn?.addEventListener('click', async () => {
            const confirmed = await window.showConfirm?.(
                'Delete all chats?',
                'This will permanently remove every conversation.',
                'warning'
            );
            if (!confirmed) return;

            try {
                const response = await fetch('{{ route('chat.clear') }}', {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                });

                if (!response.ok) throw new Error('Unable to delete chats');

                document.querySelectorAll('[id^="chat-item-"]').forEach((item) => item.remove());
                if (typeof window.startNewChat === 'function') window.startNewChat();
                closeModal('settings-modal');
                window.showAlert?.('Chats deleted', 'All conversations were removed.', 'success');
            } catch (error) {
                window.showAlert?.('Error', error.message || 'Could not delete chats.', 'error');
            }
        });

        activateTab('general');
    });
</script>
@endpush
