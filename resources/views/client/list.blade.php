@extends('layouts.master')
@section('title')
    <title>Client Management - Technic Solder</title>
@stop
@section('content')
    <h1 class="text-2xl font-bold">Client Management</h1>

    <div class="mt-6 bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-800">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 dark:border-gray-800">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Client List</h2>
            <a href="{{ url('/client/create') }}"
               class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-1.5 px-3 rounded-lg text-xs transition-colors">
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
                <div class="mb-4 p-4 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-sm text-green-700 dark:text-green-300">
                    {{ $value }}
                </div>
            @endsession

            <div x-data="dataTable({
                rows: @js($clients->map(fn($c) => ['id' => $c->id, 'name' => $c->name, 'uuid' => $c->uuid])),
                sortKey: 'id', types: { id: 'number' }
            })">
                @include('partial.data-table.toolbar', ['placeholder' => 'Search clients...'])

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-800/50 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            <tr>
                                @include('partial.data-table.sort-header', ['key' => 'id', 'label' => '#'])
                                @include('partial.data-table.sort-header', ['key' => 'name', 'label' => 'Name'])
                                @include('partial.data-table.sort-header', ['key' => 'uuid', 'label' => 'Client UUID'])
                                <th class="px-5 py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                            <template x-for="row in paged" :key="row.id">
                                <tr>
                                    <td class="px-5 py-3 text-gray-900 dark:text-gray-100" x-text="row.id"></td>
                                    <td class="px-5 py-3 text-gray-900 dark:text-gray-100" x-text="row.name"></td>
                                    <td class="px-5 py-3 text-gray-900 dark:text-gray-100 font-mono text-xs" x-text="row.uuid"></td>
                                    <td class="px-5 py-3">
                                        <a :href="'/client/delete/' + row.id"
                                           class="bg-red-600 hover:bg-red-700 text-white font-medium py-1.5 px-3 rounded-lg text-xs transition-colors">
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
