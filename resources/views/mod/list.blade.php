@extends('layouts.master')
@section('title')
    <title>Mod Library - Technic Solder</title>
@stop
@section('content')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
        <h1 class="text-2xl font-bold">Mod Library</h1>
        <a href="{{ URL::to('mod/create') }}"
           class="self-start bg-blue-600 hover:bg-blue-700 text-white dark:bg-blue-500/15 dark:text-blue-400 dark:hover:bg-blue-500/25 font-medium py-2 px-4 rounded-lg text-sm transition-colors">
            Add Mod
        </a>
    </div>

    <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-800">
        <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-800">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Mod List</h2>
        </div>
        <div class="p-5">
            @session('success')
                <div class="mb-4 p-4 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-sm text-green-700 dark:text-green-300">
                    {{ $value }}
                </div>
            @endsession
            @include('partial.form-errors')

            <div x-data="dataTable({
                rows: @js($mods->map(fn($m) => [
                    'id' => $m->id,
                    'name' => $m->name,
                    'pretty_name' => $m->pretty_name,
                    'display_name' => ($m->pretty_name ?: $m->name) . ' ' . $m->name,
                    'latest_version' => $m->latestVersion?->version ?? 'N/A',
                    'author' => $m->author ?: 'N/A',
                    'link' => $m->link,
                ])),
                sortKey: 'display_name', types: { id: 'number' },
                searchKeys: ['name', 'pretty_name', 'display_name', 'latest_version', 'author', 'link']
            })">
                @include('partial.data-table.toolbar', ['placeholder' => 'Search mods...'])

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-800/50 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                <th class="px-5 py-3 hidden sm:table-cell" @click="sort('id')" role="button">
                                    <span class="inline-flex items-center gap-1"># <span x-show="sortKey === 'id'" x-text="sortDir === 'asc' ? '↑' : '↓'"></span></span>
                                </th>
                                @include('partial.data-table.sort-header', ['key' => 'display_name', 'label' => 'Mod Name'])
                                @include('partial.data-table.sort-header', ['key' => 'latest_version', 'label' => 'Latest Version'])
                                <th class="px-5 py-3 hidden md:table-cell" @click="sort('author')" role="button">
                                    <span class="inline-flex items-center gap-1">Author <span x-show="sortKey === 'author'" x-text="sortDir === 'asc' ? '↑' : '↓'"></span></span>
                                </th>
                                <th class="px-5 py-3 hidden lg:table-cell" @click="sort('link')" role="button">
                                    <span class="inline-flex items-center gap-1">Website <span x-show="sortKey === 'link'" x-text="sortDir === 'asc' ? '↑' : '↓'"></span></span>
                                </th>
                                <th class="px-5 py-3 hidden sm:table-cell">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                            <template x-for="row in paged" :key="row.id">
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30 transition-colors">
                                    <td class="px-5 py-3 text-gray-500 dark:text-gray-400 hidden sm:table-cell">
                                        <a :href="'/mod/view/' + row.id" class="hover:text-blue-600 dark:hover:text-blue-400" x-text="row.id"></a>
                                    </td>
                                    <td class="px-5 py-3">
                                        <a :href="'/mod/view/' + row.id" class="text-blue-600 dark:text-blue-400 hover:underline font-medium" x-text="row.pretty_name || row.name"></a>
                                        <template x-if="row.pretty_name">
                                            <span class="text-gray-500 dark:text-gray-400 text-xs ml-1" x-text="'(' + row.name + ')'"></span>
                                        </template>
                                    </td>
                                    <td class="px-5 py-3 text-gray-700 dark:text-gray-300" x-text="row.latest_version"></td>
                                    <td class="px-5 py-3 text-gray-700 dark:text-gray-300 hidden md:table-cell" x-text="row.author"></td>
                                    <td class="px-5 py-3 hidden lg:table-cell">
                                        <template x-if="row.link">
                                            <a :href="row.link" target="_blank" rel="noopener noreferrer"
                                               class="text-blue-600 dark:text-blue-400 hover:underline truncate block max-w-xs" x-text="row.link"></a>
                                        </template>
                                        <template x-if="!row.link">
                                            <span class="text-gray-400 dark:text-gray-500">N/A</span>
                                        </template>
                                    </td>
                                    <td class="px-5 py-3 hidden sm:table-cell">
                                        <a :href="'/mod/view/' + row.id"
                                           class="bg-blue-600 hover:bg-blue-700 text-white dark:bg-blue-500/15 dark:text-blue-400 dark:hover:bg-blue-500/25 font-medium py-1.5 px-3 text-xs rounded-lg transition-colors inline-block">
                                            Manage
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
