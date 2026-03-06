@extends('layouts.master')
@section('title')
    <title>Platform Key Management - Technic Solder</title>
@stop
@section('content')
    <h1 class="text-2xl font-bold">Platform Key Management</h1>

    <div class="mt-6 bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-800">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-b border-gray-200 dark:border-gray-800">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Platform Key List</h2>
            <a href="{{ URL::to('key/create') }}"
               class="self-start bg-blue-600 hover:bg-blue-700 text-white dark:bg-blue-500/15 dark:text-blue-400 dark:hover:bg-blue-500/25 font-medium py-1.5 px-3 rounded-lg text-xs transition-colors">
                Add Platform Key
            </a>
        </div>
        <div class="px-5 py-4">
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                This is the list of platform keys that have access to Solder.
            </p>

            @session('success')
                <div class="mb-4 p-4 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-sm text-green-700 dark:text-green-300">
                    {{ $value }}
                </div>
            @endsession

            <div x-data="dataTable({
                rows: @js($keys->map(fn($k) => ['id' => $k->id, 'name' => $k->name, 'api_key' => $k->api_key])),
                sortKey: 'id', types: { id: 'number' }
            })">
                @include('partial.data-table.toolbar', ['placeholder' => 'Search platform keys...'])

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-800/50 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            <tr>
                                <th class="px-5 py-3 hidden sm:table-cell cursor-pointer" @click="sort('id')">
                                    <span class="inline-flex items-center gap-1"># <span x-show="sortKey === 'id'" x-text="sortDir === 'asc' ? '↑' : '↓'"></span></span>
                                </th>
                                @include('partial.data-table.sort-header', ['key' => 'name', 'label' => 'Name'])
                                <th class="px-5 py-3 hidden sm:table-cell cursor-pointer" @click="sort('api_key')">
                                    <span class="inline-flex items-center gap-1">API Key <span x-show="sortKey === 'api_key'" x-text="sortDir === 'asc' ? '↑' : '↓'"></span></span>
                                </th>
                                <th class="px-5 py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                            <template x-for="row in paged" :key="row.id">
                                <tr>
                                    <td class="px-5 py-3 text-gray-900 dark:text-gray-100 hidden sm:table-cell" x-text="row.id"></td>
                                    <td class="px-5 py-3 text-gray-900 dark:text-gray-100">
                                        <span x-text="row.name"></span>
                                        <div class="sm:hidden font-mono text-xs text-gray-500 dark:text-gray-400 mt-0.5 break-all" x-text="row.api_key"></div>
                                    </td>
                                    <td class="px-5 py-3 text-gray-900 dark:text-gray-100 font-mono text-xs break-all hidden sm:table-cell" x-text="row.api_key"></td>
                                    <td class="px-5 py-3">
                                        <a :href="'/key/delete/' + row.id"
                                           class="bg-red-600 hover:bg-red-700 text-white dark:bg-red-500/15 dark:text-red-400 dark:hover:bg-red-500/25 font-medium py-1.5 px-3 rounded-lg text-xs transition-colors">
                                            Delete
                                        </a>
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
