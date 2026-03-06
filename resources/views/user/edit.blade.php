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
                            If you would like to change this account's password you may include new passwords below. This
                            is not required to edit an account.
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

                        <div class="flex items-center gap-3">
                            <button type="submit"
                                    class="bg-blue-600 hover:bg-blue-700 text-white dark:bg-blue-500/15 dark:text-blue-400 dark:hover:bg-blue-500/25 font-medium py-2 px-4 rounded-lg text-sm transition-colors">
                                Save User
                            </button>
                            <a href="{{ url('/user/list') }}"
                               class="bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 font-medium py-2 px-4 rounded-lg text-sm transition-colors">
                                Go Back
                            </a>
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
                                        Manage API Keys
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
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Specific Modpacks</label>
                                <div class="space-y-2">
                                    @forelse ($allModpacks as $modpack)
                                        <label for="{{ $modpack->slug }}" class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                                            <input type="checkbox"
                                                   name="modpack[]"
                                                   id="{{ $modpack->slug }}"
                                                   value="{{ $modpack->id }}"
                                                   @checked(in_array($modpack->id, $user->permission->modpacks))
                                                   class="size-4 rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500">
                                            {{ $modpack->name }}
                                        </label>
                                    @empty
                                        <p class="text-sm text-gray-500 dark:text-gray-400">No modpacks exist.</p>
                                    @endforelse
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
