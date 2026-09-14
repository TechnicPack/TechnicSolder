@extends('layouts.master')
@section('title')
    <title>{{ $modpack->name }} - Technic Solder</title>
@stop
@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Build Management - {{ $modpack->name }}</h1>
    </div>

    <div class="ui-card">
        <div class="ui-card-header flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <span class="font-semibold text-gray-900 dark:text-white">Build Management: {{ $modpack->name }}</span>
            <div class="flex items-center gap-2">
                <a href="{{ url('modpack/add-build/'.$modpack->id) }}"
                   class="ui-btn ui-btn-sm ui-btn-primary">
                    Create New Build
                </a>
                @can('create', App\Models\Modpack::class)
                    <a href="{{ url('modpack/clone/'.$modpack->id) }}"
                       class="ui-btn ui-btn-sm ui-btn-purple">
                        Clone Modpack
                    </a>
                @endcan
                <a href="{{ url('modpack/edit/'.$modpack->id) }}"
                   class="ui-btn ui-btn-sm ui-btn-warning">
                    Edit Modpack
                </a>
            </div>
        </div>
        <div class="p-5">
            @session('success')
                <div class="ui-alert ui-alert-success mb-4 p-4">
                    {{ $value }}
                </div>
            @endsession

            <div x-data="buildManager()">
                <div x-data="dataTable({
                    rows: @js($modpack->builds->map(fn($b) => [
                        'id' => $b->id,
                        'version' => $b->version,
                        'minecraft' => $b->minecraft,
                        'mod_count' => $b->modversions_count,
                        'is_published' => $b->is_published,
                        'private' => $b->private,
                        'created_at' => $b->created_at->toIso8601String(),
                        'created_at_display' => (string) $b->created_at,
                    ])),
                    sortKey: 'id', sortDir: 'desc', tableName: 'modpack-builds',
                    types: { id: 'number', mod_count: 'number', created_at: 'date' }
                })">
                    @include('partial.data-table.toolbar', ['placeholder' => 'Search builds...'])

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="ui-table-head">
                                    <th class="px-5 py-3 hidden sm:table-cell cursor-pointer" @click="sort('id')">
                                        <span class="inline-flex items-center gap-1"># <span x-show="sortKey === 'id'" x-text="sortDir === 'asc' ? '↑' : '↓'"></span></span>
                                    </th>
                                    @include('partial.data-table.sort-header', ['key' => 'version', 'label' => 'Build'])
                                    <th class="px-5 py-3 hidden md:table-cell cursor-pointer" @click="sort('minecraft')">
                                        <span class="inline-flex items-center gap-1">MC <span x-show="sortKey === 'minecraft'" x-text="sortDir === 'asc' ? '↑' : '↓'"></span></span>
                                    </th>
                                    <th class="px-5 py-3 hidden lg:table-cell cursor-pointer" @click="sort('mod_count')">
                                        <span class="inline-flex items-center gap-1">Mods <span x-show="sortKey === 'mod_count'" x-text="sortDir === 'asc' ? '↑' : '↓'"></span></span>
                                    </th>
                                    <th class="px-5 py-3">Rec</th>
                                    <th class="px-5 py-3">Latest</th>
                                    <th class="px-5 py-3 hidden sm:table-cell">Published</th>
                                    <th class="px-5 py-3 hidden md:table-cell">Private</th>
                                    <th class="px-5 py-3 hidden lg:table-cell cursor-pointer" @click="sort('created_at')">
                                        <span class="inline-flex items-center gap-1">Created <span x-show="sortKey === 'created_at'" x-text="sortDir === 'asc' ? '↑' : '↓'"></span></span>
                                    </th>
                                    <th class="px-5 py-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="ui-table-body">
                                <template x-for="row in paged" :key="row.id">
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30 transition-colors">
                                        <td class="px-5 py-3 text-gray-600 dark:text-gray-400 hidden sm:table-cell" x-text="row.id"></td>
                                        <td class="px-5 py-3 text-gray-900 dark:text-gray-100 font-medium" x-text="row.version"></td>
                                        <td class="px-5 py-3 text-gray-600 dark:text-gray-400 hidden md:table-cell" x-text="row.minecraft"></td>
                                        <td class="px-5 py-3 text-gray-600 dark:text-gray-400 hidden lg:table-cell" x-text="row.mod_count"></td>
                                        <td class="px-5 py-3">
                                            <input autocomplete="off"
                                                   type="radio"
                                                   name="recommended"
                                                   :value="row.version"
                                                   :checked="row.version === '{{ $modpack->recommended }}'"
                                                   @change="setRecommended($event.target.value)"
                                                   class="text-blue-600 focus:ring-blue-500 dark:bg-gray-800 border-gray-300 dark:border-gray-600">
                                        </td>
                                        <td class="px-5 py-3">
                                            <input autocomplete="off"
                                                   type="radio"
                                                   name="latest"
                                                   :value="row.version"
                                                   :checked="row.version === '{{ $modpack->latest }}'"
                                                   @change="setLatest($event.target.value)"
                                                   class="text-blue-600 focus:ring-blue-500 dark:bg-gray-800 border-gray-300 dark:border-gray-600">
                                        </td>
                                        <td class="px-5 py-3 hidden sm:table-cell">
                                            <input autocomplete="off"
                                                   type="checkbox"
                                                   :checked="row.is_published"
                                                   @change="togglePublished(row.id, $event.target.checked)"
                                                   class="ui-checkbox dark:bg-gray-800">
                                        </td>
                                        <td class="px-5 py-3 hidden md:table-cell">
                                            <input autocomplete="off"
                                                   type="checkbox"
                                                   :checked="row.private"
                                                   @change="togglePrivate(row.id, $event.target.checked)"
                                                   class="ui-checkbox dark:bg-gray-800">
                                        </td>
                                        <td class="px-5 py-3 text-gray-600 dark:text-gray-400 hidden lg:table-cell" x-text="row.created_at_display"></td>
                                        <td class="px-5 py-3">
                                            <div class="flex items-center gap-2">
                                                <a :href="'/modpack/build/' + row.id"
                                                   class="ui-btn ui-btn-sm ui-btn-primary">
                                                    Manage
                                                </a>
                                                <a :href="'/modpack/build/' + row.id + '/edit'"
                                                   class="ui-btn ui-btn-sm ui-btn-warning">
                                                    Edit
                                                </a>
                                                <a :href="'/modpack/build/' + row.id + '/delete'"
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
    </div>
@endsection
@push('scripts')
    <script>
        function buildManager() {
            return {
                modpackId: {{ $modpack->id }},

                async setRecommended(version) {
                    try {
                        const data = await window.ajaxPost(
                            `{{ url('modpack/modify/recommended') }}?modpack=${this.modpackId}&recommended=${encodeURIComponent(version)}`
                        );
                        Alpine.store('toasts').add(data.success, 'success');
                    } catch (error) {
                        Alpine.store('toasts').add(error.message || 'An unknown error occurred', 'error');
                    }
                },

                async setLatest(version) {
                    try {
                        const data = await window.ajaxPost(
                            `{{ url('modpack/modify/latest') }}?modpack=${this.modpackId}&latest=${encodeURIComponent(version)}`
                        );
                        Alpine.store('toasts').add(data.success, 'success');
                    } catch (error) {
                        Alpine.store('toasts').add(error.message || 'An unknown error occurred', 'error');
                    }
                },

                async togglePublished(buildId, checked) {
                    try {
                        const data = await window.ajaxPost(
                            `{{ url('modpack/modify/published') }}?build=${buildId}&published=${checked ? 1 : 0}`
                        );
                        Alpine.store('toasts').add(data.success, 'success');
                    } catch (error) {
                        Alpine.store('toasts').add(error.message || 'An unknown error occurred', 'error');
                    }
                },

                async togglePrivate(buildId, checked) {
                    try {
                        const data = await window.ajaxPost(
                            `{{ url('modpack/modify/private') }}?build=${buildId}&private=${checked ? 1 : 0}`
                        );
                        Alpine.store('toasts').add(data.success, 'success');
                    } catch (error) {
                        Alpine.store('toasts').add(error.message || 'An unknown error occurred', 'error');
                    }
                }
            };
        }
    </script>
@endpush
