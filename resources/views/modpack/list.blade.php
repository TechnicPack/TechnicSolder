@extends('layouts.master')
@section('title')
    <title>Modpack Management - Technic Solder</title>
@stop
@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Modpack Management</h1>
    </div>

    <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-800">
        <div class="px-5 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-gray-200 dark:border-gray-800">
            <span class="font-semibold text-gray-900 dark:text-white">Modpack List</span>
            <a href="{{ url('modpack/create') }}"
               class="self-start bg-green-600 hover:bg-green-700 text-white dark:bg-green-500/15 dark:text-green-400 dark:hover:bg-green-500/25 font-medium py-1.5 px-3 rounded-lg text-xs transition-colors">
                Create Modpack
            </a>
        </div>
        <div class="p-5">
            @session('success')
                <div class="mb-4 p-4 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-sm text-green-700 dark:text-green-300">
                    {{ $value }}
                </div>
            @endsession
            @include('partial.form-errors')

            <div x-data="dataTable({
                rows: @js($modpacks->map(fn($m) => [
                    'id' => $m->id,
                    'name' => $m->name,
                    'slug' => $m->slug,
                    'recommended' => $m->recommended ?: 'N/A',
                    'latest' => $m->latest ?: 'N/A',
                    'hidden' => $m->hidden,
                    'private' => $m->private,
                ])),
                sortKey: 'name', tableName: 'modpack-list',
            })">
                @include('partial.data-table.toolbar', ['placeholder' => 'Search modpacks...'])

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-800/50 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                @include('partial.data-table.sort-header', ['key' => 'name', 'label' => 'Name'])
                                @include('partial.data-table.sort-header', ['key' => 'slug', 'label' => 'Slug'])
                                <th class="px-5 py-3 hidden lg:table-cell cursor-pointer" @click="sort('recommended')">
                                    <span class="inline-flex items-center gap-1">Rec <span x-show="sortKey === 'recommended'" x-text="sortDir === 'asc' ? '↑' : '↓'"></span></span>
                                </th>
                                <th class="px-5 py-3 hidden lg:table-cell cursor-pointer" @click="sort('latest')">
                                    <span class="inline-flex items-center gap-1">Latest <span x-show="sortKey === 'latest'" x-text="sortDir === 'asc' ? '↑' : '↓'"></span></span>
                                </th>
                                <th class="px-5 py-3 hidden sm:table-cell cursor-pointer" @click="sort('hidden')">
                                    <span class="inline-flex items-center gap-1">Hidden <span x-show="sortKey === 'hidden'" x-text="sortDir === 'asc' ? '↑' : '↓'"></span></span>
                                </th>
                                <th class="px-5 py-3 hidden sm:table-cell cursor-pointer" @click="sort('private')">
                                    <span class="inline-flex items-center gap-1">Private <span x-show="sortKey === 'private'" x-text="sortDir === 'asc' ? '↑' : '↓'"></span></span>
                                </th>
                                <th class="px-5 py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                            <template x-for="row in paged" :key="row.id">
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30 transition-colors">
                                    <td class="px-5 py-3 text-gray-900 dark:text-gray-100 font-medium" x-text="row.name"></td>
                                    <td class="px-5 py-3 text-gray-600 dark:text-gray-400" x-text="row.slug"></td>
                                    <td class="px-5 py-3 text-gray-600 dark:text-gray-400 hidden lg:table-cell" x-text="row.recommended"></td>
                                    <td class="px-5 py-3 text-gray-600 dark:text-gray-400 hidden lg:table-cell" x-text="row.latest"></td>
                                    <td class="px-5 py-3 hidden sm:table-cell">
                                        <template x-if="row.hidden">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300">Yes</span>
                                        </template>
                                        <template x-if="!row.hidden">
                                            <span class="text-gray-500 dark:text-gray-400">No</span>
                                        </template>
                                    </td>
                                    <td class="px-5 py-3 hidden sm:table-cell">
                                        <template x-if="row.private">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300">Yes</span>
                                        </template>
                                        <template x-if="!row.private">
                                            <span class="text-gray-500 dark:text-gray-400">No</span>
                                        </template>
                                    </td>
                                    <td class="px-5 py-3">
                                        <div class="flex items-center gap-2">
                                            <a :href="'/modpack/view/' + row.id"
                                               class="bg-yellow-500 hover:bg-yellow-600 text-white dark:bg-yellow-500/15 dark:text-yellow-400 dark:hover:bg-yellow-500/25 font-medium py-1.5 px-3 rounded-lg text-xs transition-colors">
                                                Manage Builds
                                            </a>
                                            <a :href="'/modpack/edit/' + row.id"
                                               class="bg-blue-600 hover:bg-blue-700 text-white dark:bg-blue-500/15 dark:text-blue-400 dark:hover:bg-blue-500/25 font-medium py-1.5 px-3 rounded-lg text-xs transition-colors">
                                                Edit
                                            </a>
                                            <a :href="'/modpack/delete/' + row.id"
                                               class="bg-red-600 hover:bg-red-700 text-white dark:bg-red-500/15 dark:text-red-400 dark:hover:bg-red-500/25 font-medium py-1.5 px-3 rounded-lg text-xs transition-colors">
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
