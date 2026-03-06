@extends('layouts.master')
@section('title')
    <title>{{ $user->username }} - Technic Solder</title>
@stop
@section('content')
    <h1 class="text-2xl font-bold">User Management</h1>

    <div class="mt-6 bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-800">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-b border-gray-200 dark:border-gray-800">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                Edit User: {{ $user->email }}
            </h2>
            <span class="text-xs text-gray-500 dark:text-gray-400">
                <span class="font-medium">Last Updated By:</span>
                {{ $userUpdatedBy?->username ?? 'N/A' }} - <em>{{ $user->updated_by_ip ?: "N/A" }}</em>
            </span>
        </div>
        <div class="px-5 py-4">
            @include('partial.form-errors')

            @session('success')
                <div class="mb-4 p-4 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-sm text-green-700 dark:text-green-300">
                    {{ $value }}
                </div>
            @endsession

            <form action="{{ url()->current() }}" method="post" accept-charset="UTF-8">
                @csrf
                <input type="hidden" name="edit-user" value="1">

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    {{-- Left column: Account details --}}
                    <div>
                        <div class="mb-4">
                            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email Address</label>
                            <input type="text"
                                   name="email"
                                   id="email"
                                   value="{{ $user->email }}"
                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors">
                        </div>

                        <div class="mb-4">
                            <label for="username" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Username</label>
                            <input type="text"
                                   name="username"
                                   id="username"
                                   value="{{ $user->username }}"
                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors">
                        </div>

                        <hr class="my-4 border-gray-200 dark:border-gray-700">

                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                            Leave blank to keep the current password.
                        </p>

                        <div class="mb-4">
                            <label for="password1" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Password</label>
                            <input type="password"
                                   name="password1"
                                   id="password1"
                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors">
                        </div>

                        <div class="mb-4">
                            <label for="password2" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Password Again</label>
                            <input type="password"
                                   name="password2"
                                   id="password2"
                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors">
                        </div>

                    </div>

                    {{-- Right column: Permissions --}}
                    <div>
                        @if (Auth::user()->permission->solder_full || Auth::user()->permission->solder_users)
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Permissions</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                Please select the level of access this user will be given. The "Solderwide" permission
                                is required to access a specific section. Mod and Modpack user permissions are displayed
                                in their corresponding sections.
                            </p>

                            {{-- Solderwide --}}
                            <div class="mb-5">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Solderwide</label>
                                <div class="space-y-2">
                                    <label for="solder-full" class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                                        <input type="checkbox" name="solder-full" id="solder-full"
                                               @checked($user->permission->solder_full)
                                               class="size-4 rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500">
                                        Full Solder Access (Blanket permission)
                                    </label>
                                    <label for="manage-users" class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                                        <input type="checkbox" name="manage-users" id="manage-users"
                                               @checked($user->permission->solder_users)
                                               class="size-4 rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500">
                                        Manage Users
                                    </label>
                                    <label for="manage-keys" class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                                        <input type="checkbox" name="manage-keys" id="manage-keys"
                                               @checked($user->permission->solder_keys)
                                               class="size-4 rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500">
                                        Manage Platform Keys
                                    </label>
                                    <label for="manage-clients" class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                                        <input type="checkbox" name="manage-clients" id="manage-clients"
                                               @checked($user->permission->solder_clients)
                                               class="size-4 rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500">
                                        Manage Clients
                                    </label>
                                </div>
                            </div>

                            {{-- Mod Library --}}
                            <div class="mb-5">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Mod Library</label>
                                <div class="space-y-2">
                                    <label for="mod-create" class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                                        <input type="checkbox" name="mod-create" id="mod-create"
                                               @checked($user->permission->mods_create)
                                               class="size-4 rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500">
                                        Create Mods
                                    </label>
                                    <label for="mod-manage" class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                                        <input type="checkbox" name="mod-manage" id="mod-manage"
                                               @checked($user->permission->mods_manage)
                                               class="size-4 rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500">
                                        Manage Mods
                                    </label>
                                    <label for="mod-delete" class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                                        <input type="checkbox" name="mod-delete" id="mod-delete"
                                               @checked($user->permission->mods_delete)
                                               class="size-4 rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500">
                                        Delete Mods
                                    </label>
                                </div>
                            </div>

                            {{-- General Modpack Access --}}
                            <div class="mb-5">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">General Modpack Access</label>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">
                                    General Modpack Access permissions are required before granting access to a specific
                                    modpack. Users without these permission will not be able to perform stated actions
                                    even if the specific modpack is selected.
                                </p>
                                <div class="space-y-2">
                                    <label for="modpack-create" class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                                        <input type="checkbox" name="modpack-create" id="modpack-create"
                                               @checked($user->permission->modpacks_create)
                                               class="size-4 rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500">
                                        Create Modpacks
                                    </label>
                                    <label for="modpack-manage" class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                                        <input type="checkbox" name="modpack-manage" id="modpack-manage"
                                               @checked($user->permission->modpacks_manage)
                                               class="size-4 rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500">
                                        Manage Modpacks
                                    </label>
                                    <label for="modpack-delete" class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                                        <input type="checkbox" name="modpack-delete" id="modpack-delete"
                                               @checked($user->permission->modpacks_delete)
                                               class="size-4 rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500">
                                        Delete Modpacks
                                    </label>
                                </div>
                            </div>

                            {{-- Specific Modpacks --}}
                            <div class="mb-5">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Specific Modpacks</label>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Select which modpacks this user can access. Changes take effect when you save.</p>
                                @if ($allModpacks->isEmpty())
                                    <p class="text-sm text-gray-500 dark:text-gray-400">No modpacks exist.</p>
                                @else
                                    <div x-data="{
                                        options: @js($allModpacks->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)->map(fn($m) => ['id' => $m->id, 'name' => $m->name])->values()),
                                        selected: @js(array_map('intval', $user->permission->modpacks)),
                                        search: '',
                                        open: false,
                                        get filtered() {
                                            const s = this.search.toLowerCase();
                                            return this.options.filter(o => !s || o.name.toLowerCase().includes(s));
                                        },
                                        get selectedNames() {
                                            return this.options.filter(o => this.selected.includes(o.id));
                                        },
                                        toggle(id) {
                                            const idx = this.selected.indexOf(id);
                                            if (idx === -1) this.selected.push(id);
                                            else this.selected.splice(idx, 1);
                                        },
                                        remove(id) {
                                            this.selected = this.selected.filter(s => s !== id);
                                        },
                                        selectAll() {
                                            const ids = this.filtered.map(o => o.id);
                                            ids.forEach(id => { if (!this.selected.includes(id)) this.selected.push(id); });
                                        },
                                        deselectAll() {
                                            const ids = this.filtered.map(o => o.id);
                                            this.selected = this.selected.filter(id => !ids.includes(id));
                                        },
                                    }">
                                        {{-- Hidden inputs for form submission --}}
                                        <template x-for="id in selected" :key="id">
                                            <input type="hidden" name="modpack[]" :value="id">
                                        </template>

                                        {{-- Selected pills --}}
                                        <div class="flex flex-wrap gap-1.5 mb-3" x-show="selected.length > 0">
                                            <template x-for="mp in selectedNames" :key="mp.id">
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-500/20 text-blue-800 dark:text-blue-200 border border-transparent dark:border-blue-500/30">
                                                    <span x-text="mp.name"></span>
                                                    <button type="button" @click="remove(mp.id)" class="hover:text-blue-600 dark:hover:text-blue-100">&times;</button>
                                                </span>
                                            </template>
                                        </div>

                                        {{-- Dropdown trigger --}}
                                        <div class="relative" @click.outside="open = false">
                                            <input type="text"
                                                   x-model="search"
                                                   @focus="open = true"
                                                   @click="open = true"
                                                   @keydown.escape="open = false"
                                                   placeholder="Search modpacks..."
                                                   autocomplete="off"
                                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors">

                                            {{-- Dropdown --}}
                                            <div x-show="open"
                                                 x-transition
                                                 class="absolute z-20 mt-1 w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg max-h-60 overflow-y-auto"
                                                 style="display: none">
                                                <div class="sticky top-0 flex items-center gap-2 px-3 py-2 bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 text-xs">
                                                    <button type="button" @click="selectAll()" class="text-blue-600 dark:text-blue-400 hover:underline">Select all</button>
                                                    <span class="text-gray-300 dark:text-gray-600">|</span>
                                                    <button type="button" @click="deselectAll()" class="text-blue-600 dark:text-blue-400 hover:underline">Deselect all</button>
                                                    <span class="ml-auto text-gray-500 dark:text-gray-400" x-text="selected.length + ' selected'"></span>
                                                </div>
                                                <template x-for="option in filtered" :key="option.id">
                                                    <button type="button"
                                                            @click="toggle(option.id)"
                                                            class="w-full text-left px-3 py-2 text-sm flex items-center gap-2 hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-colors"
                                                            :class="selected.includes(option.id) ? 'text-blue-700 dark:text-blue-300' : 'text-gray-900 dark:text-gray-100'">
                                                        <span class="w-4 h-4 shrink-0 rounded border flex items-center justify-center text-xs"
                                                              :class="selected.includes(option.id)
                                                                  ? 'bg-blue-600 border-blue-600 text-white'
                                                                  : 'border-gray-300 dark:border-gray-600'">
                                                            <span x-show="selected.includes(option.id)">&#10003;</span>
                                                        </span>
                                                        <span x-text="option.name"></span>
                                                    </button>
                                                </template>
                                                <div x-show="filtered.length === 0" class="px-3 py-4 text-sm text-gray-500 dark:text-gray-400 text-center">
                                                    No modpacks match your search
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                <div class="flex items-center gap-3 mt-6">
                    <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white dark:bg-blue-500/15 dark:text-blue-400 dark:hover:bg-blue-500/25 font-medium py-2 px-4 rounded-lg text-sm transition-colors">
                        Save User
                    </button>
                    <a href="{{ Auth::id() === $user->id ? url('/dashboard') : url('/user/list') }}"
                       class="bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 font-medium py-2 px-4 rounded-lg text-sm transition-colors">
                        Go Back
                    </a>
                </div>
            </form>

            @if ($showTwoFactor)
                <hr class="my-6 border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Two-Factor Authentication</h3>

                <div x-data="{
                    showModal: false,
                    password: '',
                    error: '',
                    targetForm: null,
                    confirm(formEl) {
                        this.targetForm = formEl;
                        this.password = '';
                        this.error = '';
                        this.showModal = true;
                        this.$nextTick(() => this.$refs.passwordInput.focus());
                    },
                    async submit() {
                        this.error = '';
                        const res = await fetch('{{ url('/user/confirm-password') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            },
                            body: JSON.stringify({ password: this.password }),
                        });
                        if (res.status === 201) {
                            this.showModal = false;
                            this.targetForm.submit();
                        } else {
                            this.error = 'Incorrect password.';
                            this.$refs.passwordInput.focus();
                        }
                    },
                }">
                    @if (! $user->two_factor_secret)
                        {{-- Not enabled --}}
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                            Add an extra layer of security to your account using TOTP two-factor authentication.
                        </p>
                        <form x-ref="enableForm" method="POST" action="{{ url('/user/two-factor-authentication') }}">
                            @csrf
                            <button type="button" @click="confirm($refs.enableForm)"
                                    class="bg-blue-600 hover:bg-blue-700 text-white dark:bg-blue-500/15 dark:text-blue-400 dark:hover:bg-blue-500/25 font-medium py-2 px-4 rounded-lg text-sm transition-colors">
                                Enable 2FA
                            </button>
                        </form>
                    @elseif (! $user->two_factor_confirmed_at)
                        {{-- Enabled but not confirmed --}}
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                            Scan this QR code with your authenticator app, then enter the code below to confirm.
                        </p>
                        <div class="my-4 inline-block p-4 bg-white rounded-lg">
                            {!! $qrCodeSvg !!}
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-4 font-mono break-all">
                            Secret: {{ decrypt($user->two_factor_secret) }}
                        </p>
                        @error('code', 'confirmTwoFactorAuthentication')
                            <div class="mb-3 p-3 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-sm text-red-700 dark:text-red-300">
                                {{ $message }}
                            </div>
                        @enderror
                        <form method="POST" action="{{ url('/user/confirmed-two-factor-authentication') }}" class="mb-3">
                            @csrf
                            <div class="flex items-end gap-2">
                                <div class="flex-1">
                                    <label for="confirm-code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Confirm Code</label>
                                    <input type="text" name="code" id="confirm-code" inputmode="numeric" autocomplete="one-time-code"
                                           class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors"
                                           placeholder="000000">
                                </div>
                                <button type="submit"
                                        class="bg-blue-600 hover:bg-blue-700 text-white dark:bg-blue-500/15 dark:text-blue-400 dark:hover:bg-blue-500/25 font-medium py-2 px-4 rounded-lg text-sm transition-colors">
                                    Confirm
                                </button>
                            </div>
                        </form>
                        <form x-ref="cancelForm" method="POST" action="{{ url('/user/two-factor-authentication') }}">
                            @csrf
                            @method('DELETE')
                            <button type="button" @click="confirm($refs.cancelForm)" class="text-sm text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300">
                                Cancel Setup
                            </button>
                        </form>
                    @else
                        {{-- Confirmed --}}
                        <div class="flex items-center gap-2 mb-3">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">
                                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z"/></svg>
                                2FA Enabled
                            </span>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                            Two-factor authentication is active. You will be asked for a code from your authenticator app when logging in.
                        </p>
                        @if ($recoveryCodes)
                            <div class="mb-4 p-4 rounded-lg bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 max-w-2xl">
                                <p class="text-sm font-medium text-yellow-800 dark:text-yellow-300 mb-2">
                                    Save these recovery codes in a secure location. They can be used to access your account if you lose your authenticator device. Each code can only be used once.
                                </p>
                                <div class="grid grid-cols-2 gap-1 p-3 bg-white dark:bg-gray-900 rounded-lg mb-2">
                                    @foreach ($recoveryCodes as $code)
                                        <code class="text-xs font-mono text-gray-700 dark:text-gray-300">{{ $code }}</code>
                                    @endforeach
                                </div>
                                <div class="flex items-center gap-2">
                                    <button type="button"
                                            onclick="const a=document.createElement('a');a.href=window.URL.createObjectURL(new Blob([{{ Js::from(implode("\n", $recoveryCodes)) }}],{type:'text/plain'}));a.download='solder-recovery-codes.txt';a.click();window.URL.revokeObjectURL(a.href)"
                                            class="text-xs font-medium text-yellow-800 dark:text-yellow-300 hover:text-yellow-900 dark:hover:text-yellow-200 underline">
                                        Download codes
                                    </button>
                                    <span class="text-xs text-yellow-700 dark:text-yellow-400">
                                        &mdash; these codes will not be shown again.
                                    </span>
                                </div>
                            </div>
                        @endif
                        <div class="flex items-center gap-3">
                            <form x-ref="regenForm" method="POST" action="{{ url('/user/two-factor-recovery-codes') }}">
                                @csrf
                                <button type="button" @click="confirm($refs.regenForm)"
                                        class="bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 font-medium py-2 px-4 rounded-lg text-sm transition-colors">
                                    Regenerate Recovery Codes
                                </button>
                            </form>
                            <form x-ref="disableForm" method="POST" action="{{ url('/user/two-factor-authentication') }}">
                                @csrf
                                @method('DELETE')
                                <button type="button" @click="confirm($refs.disableForm)"
                                        class="bg-red-600 hover:bg-red-700 text-white dark:bg-red-500/15 dark:text-red-400 dark:hover:bg-red-500/25 font-medium py-2 px-4 rounded-lg text-sm transition-colors">
                                    Disable 2FA
                                </button>
                            </form>
                        </div>
                    @endif

                    {{-- Password confirmation modal --}}
                    <div x-show="showModal" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" style="display: none" @keydown.escape.window="showModal = false">
                        <div @click.outside="showModal = false" class="bg-white dark:bg-gray-900 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 w-full max-w-md mx-4 p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Confirm Password</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Please confirm your password to continue.</p>
                            <div class="mb-4">
                                <input type="password" x-model="password" x-ref="passwordInput" @keydown.enter="submit()"
                                       placeholder="Password"
                                       class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors">
                                <p x-show="error" x-text="error" class="mt-1 text-sm text-red-600 dark:text-red-400"></p>
                            </div>
                            <div class="flex items-center gap-3 justify-end">
                                <button type="button" @click="showModal = false"
                                        class="bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 font-medium py-2 px-4 rounded-lg text-sm transition-colors">
                                    Cancel
                                </button>
                                <button type="button" @click="submit()"
                                        class="bg-blue-600 hover:bg-blue-700 text-white dark:bg-blue-500/15 dark:text-blue-400 dark:hover:bg-blue-500/25 font-medium py-2 px-4 rounded-lg text-sm transition-colors">
                                    Confirm
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if (!empty($adminViewingOther2FA))
                <hr class="my-6 border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Two-Factor Authentication</h3>
                <div class="flex items-center gap-2 mb-3">
                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md text-xs font-medium bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">
                        2FA Enabled
                    </span>
                </div>
                <form method="POST" action="{{ route('user.reset2fa', $user->id) }}">
                    @csrf
                    <button type="submit"
                            class="bg-red-600 hover:bg-red-700 text-white dark:bg-red-500/15 dark:text-red-400 dark:hover:bg-red-500/25 font-medium py-2 px-4 rounded-lg text-sm transition-colors"
                            onclick="return confirm('Are you sure you want to reset this user\'s 2FA?')">
                        Reset 2FA
                    </button>
                </form>
            @endif

            @if (Auth::id() === $user->id)
                <hr class="my-6 border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">API Tokens</h3>

                @session('newToken')
                    <div class="mb-4 p-4 rounded-lg bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 max-w-2xl overflow-hidden">
                        <p class="text-sm font-medium text-yellow-800 dark:text-yellow-300 mb-1">
                            Your new API token. Copy it now — it won't be shown again:
                        </p>
                        <div class="flex items-center gap-2">
                            <code class="text-xs font-mono text-yellow-900 dark:text-yellow-200 bg-yellow-100 dark:bg-yellow-900/40 px-2 py-1 rounded min-w-0 flex-1 truncate">{{ $value }}</code>
                            <button type="button" x-data="{ copied: false }"
                                    @click="navigator.clipboard.writeText('{{ $value }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                    class="shrink-0 bg-yellow-200 hover:bg-yellow-300 dark:bg-yellow-800 dark:hover:bg-yellow-700 text-yellow-900 dark:text-yellow-200 font-medium py-1 px-2 rounded text-xs"
                                    x-text="copied ? 'Copied!' : 'Copy'">
                            </button>
                        </div>
                    </div>
                @endsession

                <form method="POST" action="{{ route('user.token.create') }}" class="mb-4">
                    @csrf
                    <div class="flex items-end gap-2">
                        <div class="flex-1">
                            <label for="token_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Token Name</label>
                            <input type="text" name="token_name" id="token_name" placeholder="e.g. CI/CD Pipeline"
                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors">
                        </div>
                        <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white dark:bg-blue-500/15 dark:text-blue-400 dark:hover:bg-blue-500/25 font-medium py-2 px-4 rounded-lg text-sm transition-colors">
                            Create Token
                        </button>
                    </div>
                </form>

                @php $tokens = $user->tokens()->select(['id', 'name', 'last_used_at', 'created_at'])->get(); @endphp
                @if ($tokens->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 dark:bg-gray-800/50 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                <tr>
                                    <th class="px-3 py-2">Name</th>
                                    <th class="px-3 py-2">Created</th>
                                    <th class="px-3 py-2">Last Used</th>
                                    <th class="px-3 py-2"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                                @foreach ($tokens as $token)
                                    <tr>
                                        <td class="px-3 py-2 text-gray-900 dark:text-gray-100">{{ $token->name }}</td>
                                        <td class="px-3 py-2 text-gray-600 dark:text-gray-400">{{ $token->created_at->diffForHumans() }}</td>
                                        <td class="px-3 py-2 text-gray-600 dark:text-gray-400">{{ $token->last_used_at?->diffForHumans() ?? 'Never' }}</td>
                                        <td class="px-3 py-2">
                                            <form method="POST" action="{{ route('user.token.delete', $token->id) }}">
                                                @csrf
                                                <button type="submit"
                                                        class="bg-red-600 hover:bg-red-700 text-white dark:bg-red-500/15 dark:text-red-400 dark:hover:bg-red-500/25 font-medium py-1 px-2 rounded text-xs transition-colors"
                                                        onclick="return confirm('Revoke this token?')">
                                                    Revoke
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-sm text-gray-500 dark:text-gray-400">No API tokens yet.</p>
                @endif
            @endif
        </div>
    </div>
@endsection
