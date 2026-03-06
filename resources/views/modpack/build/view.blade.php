@extends('layouts.master')
@section('title')
    <title>{{ $build->version }} - {{ $build->modpack->name }} - Technic Solder</title>
@stop
@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Build Management</h1>
    </div>

    {{-- Build info card --}}
    <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-800 mb-6">
        <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-800 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <span class="font-semibold text-gray-900 dark:text-white">{{ $build->modpack->name }} &mdash; build {{ $build->version }}</span>
            <div class="flex items-center gap-2">
                <button onclick="window.location.reload()"
                        class="bg-blue-600 hover:bg-blue-700 text-white dark:bg-blue-500/15 dark:text-blue-400 dark:hover:bg-blue-500/25 font-medium py-1.5 px-3 text-xs rounded-lg transition-colors">
                    Refresh
                </button>
                <a href="{{ url('modpack/build/' . $build->id . '/edit') }}"
                   class="bg-blue-600 hover:bg-blue-700 text-white dark:bg-blue-500/15 dark:text-blue-400 dark:hover:bg-blue-500/25 font-medium py-1.5 px-3 text-xs rounded-lg transition-colors">
                    Edit
                </a>
                <a href="{{ url('modpack/view/' . $build->modpack->id) }}"
                   class="bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 font-medium py-1.5 px-3 text-xs rounded-lg transition-colors">
                    Back to modpack
                </a>
            </div>
        </div>
        <div class="px-5 py-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Build</span>
                    <p class="mt-1 text-sm font-medium text-gray-900 dark:text-white">{{ $build->version }}</p>
                </div>
                <div>
                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Minecraft version</span>
                    <p class="mt-1 text-sm font-medium text-gray-900 dark:text-white">{{ $build->minecraft }}</p>
                </div>
                <div>
                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Required Java version</span>
                    <p class="mt-1 text-sm font-medium text-gray-900 dark:text-white">{{ $build->min_java ?: 'Not set' }}</p>
                </div>
                <div>
                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Required RAM/memory</span>
                    <p class="mt-1 text-sm font-medium text-gray-900 dark:text-white">{{ $build->min_memory ? $build->min_memory . ' MB' : 'Not set' }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Live build warning --}}
    @if ($build->isLive())
        <div x-data="{ showPanels: false }" class="mb-6">
            <div class="p-4 rounded-lg bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 text-sm text-yellow-700 dark:text-yellow-400/80">
                <p>This build is currently published and not marked as private. <strong>You are editing a live build</strong>.</p>
                <p x-show="!showPanels" class="mt-2">Build management panels have been hidden.
                    <button @click="showPanels = true"
                            class="underline font-medium hover:text-yellow-800 dark:hover:text-yellow-300 transition-colors">Click here to show them</button>
                </p>
            </div>
            <template x-if="showPanels">
                <div class="mt-6">
                    @include('modpack.build._edit-panels')
                </div>
            </template>
        </div>
    @else
        @include('modpack.build._edit-panels')
    @endif
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('modSearch', () => ({
        query: '',
        selectedMod: null,
        selectedModName: '',
        showDropdown: false,
        versions: [],
        selectedVersion: '',
        loadingVersions: false,
        mods: @json($mods->map(fn($m) => ['name' => $m->name, 'pretty_name' => $m->pretty_name ?: $m->name])),

        get filteredMods() {
            if (!this.query) return this.mods;
            const q = this.query.toLowerCase();
            return this.mods.filter(m =>
                m.pretty_name.toLowerCase().includes(q) || m.name.toLowerCase().includes(q)
            );
        },

        selectMod(mod) {
            this.selectedMod = mod;
            this.selectedModName = mod.name;
            this.query = mod.pretty_name;
            this.showDropdown = false;
            this.versions = [];
            this.selectedVersion = '';
            this.loadVersions(mod.name);
        },

        clearSelection() {
            this.selectedMod = null;
            this.selectedModName = '';
            this.versions = [];
            this.selectedVersion = '';
        },

        async loadVersions(modName) {
            this.loadingVersions = true;
            try {
                const data = await window.ajax("{{ url('mod/versions') }}/" + modName);
                if (data.versions && data.versions.length > 0) {
                    this.versions = data.versions;
                    this.selectedVersion = data.versions[0];
                } else {
                    this.versions = [];
                    Alpine.store('toasts').add('No mod versions found for ' + (data.pretty_name || modName), 'warning');
                }
            } catch (e) {
                Alpine.store('toasts').add('Failed to load mod versions', 'error');
            }
            this.loadingVersions = false;
        },

        async addToBuild() {
            if (!this.selectedVersion) {
                Alpine.store('toasts').add('Please select a mod version', 'warning');
                return;
            }
            try {
                const params = new URLSearchParams();
                params.append('_token', window.csrfToken);
                params.append('build', '{{ $build->id }}');
                params.append('action', 'add');
                params.append('mod-name', this.selectedModName);
                params.append('mod-version', this.selectedVersion);

                const res = await fetch("{{ url('modpack/modify/add') }}", {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: params,
                });
                const data = await res.json();

                if (data.status === 'success') {
                    Alpine.store('toasts').add('Mod ' + data.pretty_name + ' added at ' + data.version, 'success');
                    // Add a row to the mod list
                    this.$dispatch('mod-added', {
                        pretty_name: data.pretty_name,
                        version: data.version,
                    });
                } else {
                    Alpine.store('toasts').add('Unable to add mod. Reason: ' + data.reason, 'warning');
                }
            } catch (e) {
                Alpine.store('toasts').add('Failed to add mod: ' + e.message, 'error');
            }
        }
    }));
});
</script>
@endpush
