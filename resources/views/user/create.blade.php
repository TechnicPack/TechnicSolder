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

                        <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white dark:bg-blue-500/15 dark:text-blue-400 dark:hover:bg-blue-500/25 font-medium py-2 px-4 rounded-lg text-sm transition-colors">
                            Create User
                        </button>
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
                                    Manage API Keys
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
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Specific Modpacks</label>
                            <div class="space-y-2">
                                @foreach ($allModpacks as $modpack)
                                    <label for="{{ $modpack->slug }}" class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                                        <input type="checkbox"
                                               name="modpack[]"
                                               id="{{ $modpack->slug }}"
                                               value="{{ $modpack->id }}"
                                               class="size-4 rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500">
                                        {{ $modpack->name }}
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
