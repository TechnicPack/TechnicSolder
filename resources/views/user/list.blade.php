@extends('layouts.master')
@section('title')
    <title>User Management - Technic Solder</title>
@stop
@section('content')
    <h1 class="text-2xl font-bold">User Management</h1>

    <div class="ui-card mt-6">
        <div class="ui-card-header flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">User List</h2>
            <a href="{{ URL::to('user/create') }}"
               class="ui-btn ui-btn-sm ui-btn-primary self-start">
                Create User
            </a>
        </div>
        <div class="px-5 py-4">
            @include('partial.form-errors')

            <div x-data="dataTable({
                rows: @js($users->map(fn($u) => [
                    'id' => $u->id,
                    'email' => $u->email,
                    'username' => $u->username,
                    'two_factor' => (bool) $u->two_factor_confirmed_at,
                    'updated_by' => ($u->updated_by_user?->username ?? 'N/A') . ' - ' . ($u->updated_by_ip ?: 'N/A'),
                    'updated_at' => $u->updated_at->toIso8601String(),
                    'updated_at_display' => date_format($u->updated_at, 'r'),
                ])),
                sortKey: 'id', tableName: 'user-list', types: { id: 'number', updated_at: 'date' },
                searchKeys: ['id', 'email', 'username', 'updated_by', 'updated_at_display']
            })">
                @include('partial.data-table.toolbar', ['placeholder' => 'Search users...'])

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="ui-table-head">
                            <tr>
                                <th class="px-5 py-3 hidden sm:table-cell cursor-pointer" @click="sort('id')">
                                    <span class="inline-flex items-center gap-1">ID # <span x-show="sortKey === 'id'" x-text="sortDir === 'asc' ? '↑' : '↓'"></span></span>
                                </th>
                                @include('partial.data-table.sort-header', ['key' => 'email', 'label' => 'Email'])
                                <th class="px-5 py-3 hidden sm:table-cell cursor-pointer" @click="sort('username')">
                                    <span class="inline-flex items-center gap-1">Username <span x-show="sortKey === 'username'" x-text="sortDir === 'asc' ? '↑' : '↓'"></span></span>
                                </th>
                                <th class="px-5 py-3 hidden sm:table-cell">2FA</th>
                                <th class="px-5 py-3 hidden lg:table-cell cursor-pointer" @click="sort('updated_by')">
                                    <span class="inline-flex items-center gap-1">Updated by <span x-show="sortKey === 'updated_by'" x-text="sortDir === 'asc' ? '↑' : '↓'"></span></span>
                                </th>
                                <th class="px-5 py-3 hidden md:table-cell cursor-pointer" @click="sort('updated_at')">
                                    <span class="inline-flex items-center gap-1">Updated at <span x-show="sortKey === 'updated_at'" x-text="sortDir === 'asc' ? '↑' : '↓'"></span></span>
                                </th>
                                <th class="px-5 py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="ui-table-body">
                            <template x-for="row in paged" :key="row.id">
                                <tr>
                                    <td class="px-5 py-3 text-gray-900 dark:text-gray-100 hidden sm:table-cell" x-text="row.id"></td>
                                    <td class="px-5 py-3 text-gray-900 dark:text-gray-100">
                                        <span x-text="row.email"></span>
                                        <div class="sm:hidden text-xs text-gray-500 dark:text-gray-400 mt-0.5" x-text="row.username"></div>
                                    </td>
                                    <td class="px-5 py-3 text-gray-900 dark:text-gray-100 hidden sm:table-cell" x-text="row.username"></td>
                                    <td class="px-5 py-3 hidden sm:table-cell">
                                        <span x-show="row.two_factor" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">Enabled</span>
                                        <span x-show="!row.two_factor" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">Disabled</span>
                                    </td>
                                    <td class="px-5 py-3 text-gray-600 dark:text-gray-400 hidden lg:table-cell" x-text="row.updated_by"></td>
                                    <td class="px-5 py-3 text-gray-600 dark:text-gray-400 hidden md:table-cell" x-text="row.updated_at_display"></td>
                                    <td class="px-5 py-3">
                                        <div class="flex items-center gap-2">
                                            <a :href="'/user/edit/' + row.id"
                                               class="ui-btn ui-btn-sm ui-btn-primary">
                                                Edit
                                            </a>
                                            <a :href="'/user/delete/' + row.id"
                                               class="ui-btn ui-btn-sm ui-btn-danger">
                                                Delete
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                @include('partial.data-table.pagination')
            </div>
        </div>
    </div>
@endsection
