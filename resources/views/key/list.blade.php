@extends('layouts.master')
@section('title')
    <title>Platform Key Management - Technic Solder</title>
@stop
@section('content')
    <h1 class="text-2xl font-bold">Platform Key Management</h1>

    <div class="ui-card mt-6">
        <div class="ui-card-header flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Platform Key List</h2>
            <a href="{{ URL::to('key/create') }}"
               class="ui-btn ui-btn-sm ui-btn-primary self-start">
                Add Platform Key
            </a>
        </div>
        <div class="px-5 py-4">
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                This is the list of platform keys that have access to Solder.
            </p>

            @session('success')
                <div class="ui-alert ui-alert-success mb-4 p-4">
                    {{ $value }}
                </div>
            @endsession

            <div x-data="dataTable({
                rows: @js($keys->map(fn($k) => ['id' => $k->id, 'name' => $k->name, 'api_key' => $k->api_key])),
                sortKey: 'id', tableName: 'key-list', types: { id: 'number' }
            })">
                @include('partial.data-table.toolbar', ['placeholder' => 'Search platform keys...'])

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="ui-table-head">
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
                        <tbody class="ui-table-body">
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
                                           class="ui-btn ui-btn-sm ui-btn-danger">
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
