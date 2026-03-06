@extends('layouts.master')
@section('title')
    <title>{{ $modpack->name }} - Technic Solder</title>
@stop
@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Build Management - {{ $modpack->name }}</h1>
    </div>

    <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-800">
        <div class="px-5 py-4 flex items-center justify-between border-b border-gray-200 dark:border-gray-800">
            <span class="font-semibold text-gray-900 dark:text-white">Build Management: {{ $modpack->name }}</span>
            <div class="flex items-center gap-2">
                <a href="{{ url('modpack/add-build/'.$modpack->id) }}"
                   class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-1.5 px-3 rounded-lg text-xs transition-colors">
                    Create New Build
                </a>
                <a href="{{ url('modpack/edit/'.$modpack->id) }}"
                   class="bg-yellow-500 hover:bg-yellow-600 text-white font-medium py-1.5 px-3 rounded-lg text-xs transition-colors">
                    Edit Modpack
                </a>
            </div>
        </div>
        <div class="p-5">
            @session('success')
                <div class="mb-4 p-4 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-sm text-green-700 dark:text-green-300">
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
                    sortKey: 'id', sortDir: 'desc',
                    types: { id: 'number', mod_count: 'number', created_at: 'date' }
                })">
                    @include('partial.data-table.toolbar', ['placeholder' => 'Search builds...'])

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-800/50 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    @include('partial.data-table.sort-header', ['key' => 'id', 'label' => '#'])
                                    @include('partial.data-table.sort-header', ['key' => 'version', 'label' => 'Build Number'])
                                    @include('partial.data-table.sort-header', ['key' => 'minecraft', 'label' => 'MC Version'])
                                    @include('partial.data-table.sort-header', ['key' => 'mod_count', 'label' => 'Mod Count'])
                                    <th class="px-5 py-3">Rec</th>
                                    <th class="px-5 py-3">Latest</th>
                                    <th class="px-5 py-3">Published</th>
                                    <th class="px-5 py-3">Private</th>
                                    @include('partial.data-table.sort-header', ['key' => 'created_at', 'label' => 'Created on'])
                                    <th class="px-5 py-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                                <template x-for="row in paged" :key="row.id">
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30 transition-colors">
                                        <td class="px-5 py-3 text-gray-600 dark:text-gray-400" x-text="row.id"></td>
                                        <td class="px-5 py-3 text-gray-900 dark:text-gray-100 font-medium" x-text="row.version"></td>
                                        <td class="px-5 py-3 text-gray-600 dark:text-gray-400" x-text="row.minecraft"></td>
                                        <td class="px-5 py-3 text-gray-600 dark:text-gray-400" x-text="row.mod_count"></td>
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
                                        <td class="px-5 py-3">
                                            <input autocomplete="off"
                                                   type="checkbox"
                                                   :checked="row.is_published"
                                                   @change="togglePublished(row.id, $event.target.checked)"
                                                   class="rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500 dark:bg-gray-800">
                                        </td>
                                        <td class="px-5 py-3">
                                            <input autocomplete="off"
                                                   type="checkbox"
                                                   :checked="row.private"
                                                   @change="togglePrivate(row.id, $event.target.checked)"
                                                   class="rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500 dark:bg-gray-800">
                                        </td>
                                        <td class="px-5 py-3 text-gray-600 dark:text-gray-400" x-text="row.created_at_display"></td>
                                        <td class="px-5 py-3">
                                            <div class="flex items-center gap-2">
                                                <a :href="'/modpack/build/' + row.id"
                                                   class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-1.5 px-3 rounded-lg text-xs transition-colors">
                                                    Manage
                                                </a>
                                                <a :href="'/modpack/build/' + row.id + '/edit'"
                                                   class="bg-yellow-500 hover:bg-yellow-600 text-white font-medium py-1.5 px-3 rounded-lg text-xs transition-colors">
                                                    Edit
                                                </a>
                                                <a :href="'/modpack/build/' + row.id + '/delete'"
                                                   class="bg-red-600 hover:bg-red-700 text-white font-medium py-1.5 px-3 rounded-lg text-xs transition-colors">
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
                        $store.toasts.add(data.success, 'success');
                    } catch (error) {
                        $store.toasts.add(error.message || 'An unknown error occurred', 'error');
                    }
                },

                async setLatest(version) {
                    try {
                        const data = await window.ajaxPost(
                            `{{ url('modpack/modify/latest') }}?modpack=${this.modpackId}&latest=${encodeURIComponent(version)}`
                        );
                        $store.toasts.add(data.success, 'success');
                    } catch (error) {
                        $store.toasts.add(error.message || 'An unknown error occurred', 'error');
                    }
                },

                async togglePublished(buildId, checked) {
                    try {
                        const data = await window.ajaxPost(
                            `{{ url('modpack/modify/published') }}?build=${buildId}&published=${checked ? 1 : 0}`
                        );
                        $store.toasts.add(data.success, 'success');
                    } catch (error) {
                        $store.toasts.add(error.message || 'An unknown error occurred', 'error');
                    }
                },

                async togglePrivate(buildId, checked) {
                    try {
                        const data = await window.ajaxPost(
                            `{{ url('modpack/modify/private') }}?build=${buildId}&private=${checked ? 1 : 0}`
                        );
                        $store.toasts.add(data.success, 'success');
                    } catch (error) {
                        $store.toasts.add(error.message || 'An unknown error occurred', 'error');
                    }
                }
            };
        }
    </script>
@endpush
