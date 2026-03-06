@extends('layouts.master')
@section('title')
    <title>Create User - Technic Solder</title>
@stop
@section('content')
    <h1 class="text-2xl font-bold">User Management</h1>

    <div class="mt-6 bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-800">
        <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-800">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Create User</h2>
        </div>
        <div class="px-5 py-4">
            @include('partial.form-errors')

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
                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors">
                        </div>

                        <div class="mb-4">
                            <label for="username" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Username</label>
                            <input type="text"
                                   name="username"
                                   id="username"
                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors">
                        </div>

                        <div class="mb-4">
                            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Password</label>
                            <input type="password"
                                   name="password"
                                   id="password"
                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors">
                        </div>

                    </div>

                    {{-- Right column: Permissions --}}
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Permissions</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                            Please select the level of access this user will be given. The "Solderwide" permission is
                            required to access a specific section. Mod and Modpack user permissions are displayed in
                            their corresponding sections.
                        </p>

                        {{-- Solderwide --}}
                        <div class="mb-5">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Solderwide</label>
                            <div class="space-y-2">
                                <label for="solder-full" class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                                    <input type="checkbox" name="solder-full" id="solder-full"
                                           class="size-4 rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500">
                                    Full Solder Access (Blanket permission)
                                </label>
                                <label for="manage-users" class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                                    <input type="checkbox" name="manage-users" id="manage-users"
                                           class="size-4 rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500">
                                    Manage Users
                                </label>
                                <label for="manage-keys" class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                                    <input type="checkbox" name="manage-keys" id="manage-keys"
                                           class="size-4 rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500">
                                    Manage Platform Keys
                                </label>
                                <label for="manage-clients" class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                                    <input type="checkbox" name="manage-clients" id="manage-clients"
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
                                           class="size-4 rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500">
                                    Create Mods
                                </label>
                                <label for="mod-manage" class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                                    <input type="checkbox" name="mod-manage" id="mod-manage"
                                           class="size-4 rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500">
                                    Manage Mods
                                </label>
                                <label for="mod-delete" class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                                    <input type="checkbox" name="mod-delete" id="mod-delete"
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
                                modpack. Users without these permission will not be able to perform stated actions even
                                if the specific modpack is selected.
                            </p>
                            <div class="space-y-2">
                                <label for="modpack-create" class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                                    <input type="checkbox" name="modpack-create" id="modpack-create"
                                           class="size-4 rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500">
                                    Create Modpacks
                                </label>
                                <label for="modpack-manage" class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                                    <input type="checkbox" name="modpack-manage" id="modpack-manage"
                                           class="size-4 rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500">
                                    Manage Modpacks
                                </label>
                                <label for="modpack-delete" class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                                    <input type="checkbox" name="modpack-delete" id="modpack-delete"
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
                                    selected: [],
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
                    </div>
                </div>

                <div class="flex items-center gap-3 mt-6">
                    <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white dark:bg-blue-500/15 dark:text-blue-400 dark:hover:bg-blue-500/25 font-medium py-2 px-4 rounded-lg text-sm transition-colors">
                        Create User
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
