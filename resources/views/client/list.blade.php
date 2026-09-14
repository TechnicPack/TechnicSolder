@extends('layouts.master')
@section('title')
    <title>Client Management - Technic Solder</title>
@stop
@section('content')
    <h1 class="text-2xl font-bold">Client Management</h1>

    <div class="ui-card mt-6">
        <div class="ui-card-header flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Client List</h2>
            <a href="{{ url('/client/create') }}"
               class="ui-btn ui-btn-sm ui-btn-primary self-start">
                Add Client
            </a>
        </div>
        <div class="px-5 py-4">
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                This is the client management area. Here you can register your launcher client UUID to Solder so that
                private builds will show up to you in the launcher. After a client is added to this list, they need to
                be linked to the modpacks you want them to have access to in Solder.
            </p>

            @session('success')
                <div class="ui-alert ui-alert-success mb-4 p-4">
                    {{ $value }}
                </div>
            @endsession

            <div x-data="dataTable({
                rows: @js($clients->map(fn($c) => ['id' => $c->id, 'name' => $c->name, 'uuid' => $c->uuid])),
                sortKey: 'id', tableName: 'client-list', types: { id: 'number' }
            })">
                @include('partial.data-table.toolbar', ['placeholder' => 'Search clients...'])

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="ui-table-head">
                            <tr>
                                <th class="px-5 py-3 hidden sm:table-cell cursor-pointer" @click="sort('id')">
                                    <span class="inline-flex items-center gap-1"># <span x-show="sortKey === 'id'" x-text="sortDir === 'asc' ? '↑' : '↓'"></span></span>
                                </th>
                                @include('partial.data-table.sort-header', ['key' => 'name', 'label' => 'Name'])
                                <th class="px-5 py-3 hidden sm:table-cell cursor-pointer" @click="sort('uuid')">
                                    <span class="inline-flex items-center gap-1">Client UUID <span x-show="sortKey === 'uuid'" x-text="sortDir === 'asc' ? '↑' : '↓'"></span></span>
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
                                        <div class="sm:hidden font-mono text-xs text-gray-500 dark:text-gray-400 mt-0.5 break-all" x-text="row.uuid"></div>
                                    </td>
                                    <td class="px-5 py-3 text-gray-900 dark:text-gray-100 font-mono text-xs break-all hidden sm:table-cell" x-text="row.uuid"></td>
                                    <td class="px-5 py-3">
                                        <a :href="'/client/delete/' + row.id"
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
