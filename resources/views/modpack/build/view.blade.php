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
        modHighlight: -1,
        versions: [],
        selectedVersion: '',
        versionQuery: '',
        showVersionDropdown: false,
        versionHighlight: -1,
        loadingVersions: false,
        mods: @json($mods->map(fn($m) => ['name' => $m->name, 'pretty_name' => $m->pretty_name ?: $m->name])),
        modsInBuild: new Set(@json($build->modversions->pluck('mod.name'))),

        get filteredMods() {
            let list = this.mods.filter(m => !this.modsInBuild.has(m.name));
            if (!this.query) return list;
            const q = this.query.toLowerCase();
            return list.filter(m =>
                m.pretty_name.toLowerCase().includes(q) || m.name.toLowerCase().includes(q)
            );
        },

        get filteredVersions() {
            if (!this.versionQuery) return this.versions;
            const q = this.versionQuery.toLowerCase();
            return this.versions.filter(v => v.toLowerCase().includes(q));
        },

        scrollToHighlight(ref, index) {
            this.$nextTick(() => {
                const container = this.$refs[ref];
                const item = container?.children[index];
                item?.scrollIntoView({ block: 'nearest' });
            });
        },

        modArrow(dir) {
            const len = this.filteredMods.length;
            if (!len) return;
            this.modHighlight = dir === 'down'
                ? (this.modHighlight + 1) % len
                : (this.modHighlight - 1 + len) % len;
            this.scrollToHighlight('modDropdown', this.modHighlight);
        },

        versionArrow(dir) {
            const len = this.filteredVersions.length;
            if (!len) return;
            this.versionHighlight = dir === 'down'
                ? (this.versionHighlight + 1) % len
                : (this.versionHighlight - 1 + len) % len;
            this.scrollToHighlight('versionDropdown', this.versionHighlight);
        },

        selectMod(mod) {
            this.selectedMod = mod;
            this.selectedModName = mod.name;
            this.query = mod.pretty_name;
            this.showDropdown = false;
            this.modHighlight = -1;
            this.versions = [];
            this.selectedVersion = '';
            this.versionQuery = '';
            this.loadVersions(mod.name);
        },

        selectVersion(v) {
            this.selectedVersion = v;
            this.versionQuery = v;
            this.showVersionDropdown = false;
            this.versionHighlight = -1;
        },

        clearSelection() {
            this.selectedMod = null;
            this.selectedModName = '';
            this.versions = [];
            this.selectedVersion = '';
            this.versionQuery = '';
        },

        async loadVersions(modName) {
            this.loadingVersions = true;
            try {
                const data = await window.ajax("{{ url('mod/versions') }}/" + modName);
                if (data.versions && data.versions.length > 0) {
                    this.versions = data.versions;
                    this.selectedVersion = data.versions[0];
                    this.versionQuery = data.versions[0];
                    this.$nextTick(() => {
                        this.$refs.versionInput?.focus();
                        this.showVersionDropdown = true;
                    });
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
                    this.modsInBuild.add(data.mod_name);
                    this.$dispatch('mod-added', {
                        mod_id: data.mod_id,
                        mod_name: data.mod_name,
                        pretty_name: data.pretty_name,
                        version: data.version,
                        modversion_id: data.modversion_id,
                        versions: data.versions,
                    });
                    this.query = '';
                    this.clearSelection();
                    return true;
                } else {
                    Alpine.store('toasts').add(data.reason, 'error');
                    return false;
                }
            } catch (e) {
                Alpine.store('toasts').add('Failed to add mod: ' + e.message, 'error');
                return false;
            }
        }
    }));

    Alpine.data('modList', () => ({
        buildId: {{ $build->id }},
        mods: @js($build->modversions->sortBy(fn($v) => strtolower($v->mod->pretty_name ?: $v->mod->name))->values()->map(fn($v) => [
            'mod_id' => $v->mod->id,
            'mod_name' => $v->mod->name,
            'pretty_name' => $v->mod->pretty_name ?: $v->mod->name,
            'modversion_id' => $v->pivot->modversion_id,
            'versions' => $v->mod->versions->map(fn($ver) => ['id' => $ver->id, 'version' => $ver->version]),
        ])).map(m => ({ ...m, selected_version_id: String(m.modversion_id), just_added: false, changing: false })),

        addMod(detail) {
            this.mods.unshift({
                mod_id: detail.mod_id,
                mod_name: detail.mod_name,
                pretty_name: detail.pretty_name,
                modversion_id: detail.modversion_id,
                selected_version_id: String(detail.modversion_id),
                versions: detail.versions,
                just_added: true,
                changing: false,
            });
        },

        async changeVersion(mod) {
            mod.changing = true;
            try {
                const params = new URLSearchParams();
                params.append('_token', window.csrfToken);
                params.append('build_id', String(this.buildId));
                params.append('modversion_id', String(mod.modversion_id));
                params.append('action', 'version');
                params.append('version', String(mod.selected_version_id));
                const res = await fetch('{{ url("modpack/modify/version") }}', {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                    body: params,
                });
                const data = await res.json();
                if (data.status === 'success') {
                    mod.modversion_id = mod.selected_version_id;
                    Alpine.store('toasts').add('Mod version updated', 'success');
                } else if (data.status === 'aborted') {
                    Alpine.store('toasts').add('Mod was already set to that version', 'success');
                } else {
                    Alpine.store('toasts').add('Unable to update mod version', 'warning');
                }
            } catch {
                Alpine.store('toasts').add('Failed to update mod version', 'error');
            }
            mod.changing = false;
        },

        async removeMod(mod) {
            try {
                const params = new URLSearchParams();
                params.append('_token', window.csrfToken);
                params.append('build_id', String(this.buildId));
                params.append('modversion_id', String(mod.modversion_id));
                params.append('action', 'delete');
                const res = await fetch('{{ url("modpack/modify/delete") }}', {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                    body: params,
                });
                const data = await res.json();
                if (data.status === 'success') {
                    Alpine.store('toasts').add('Mod version removed', 'success');
                    this.mods = this.mods.filter(m => m !== mod);
                    this.$dispatch('mod-removed', { mod_name: mod.mod_name });
                } else {
                    Alpine.store('toasts').add('Unable to remove mod version', 'warning');
                }
            } catch {
                Alpine.store('toasts').add('Failed to remove mod version', 'error');
            }
        },
    }));
});
</script>
@endpush
