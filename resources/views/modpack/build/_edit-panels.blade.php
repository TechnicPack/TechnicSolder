{{-- Add a mod card --}}
<div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-800 mb-6">
    <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-800">
        <span class="font-semibold text-gray-900 dark:text-white">Add a mod</span>
    </div>
    <div class="p-5" x-data="modSearch()">
        <div class="flex flex-col sm:flex-row gap-3 items-end">
            {{-- Mod name searchable select --}}
            <div class="flex-1 w-full sm:w-auto relative">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Mod name</label>
                <input type="text"
                       x-model="query"
                       @focus="showDropdown = true"
                       @input="showDropdown = true; if (!query) clearSelection()"
                       @click.outside="showDropdown = false"
                       @keydown.escape="showDropdown = false"
                       placeholder="Search for a mod..."
                       autocomplete="off"
                       class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors">
                {{-- Dropdown --}}
                <div x-show="showDropdown && filteredMods.length > 0"
                     x-transition
                     class="absolute z-20 mt-1 w-full max-h-60 overflow-y-auto bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg"
                     style="display: none">
                    <template x-for="mod in filteredMods" :key="mod.name">
                        <button type="button"
                                @click="selectMod(mod)"
                                class="w-full text-left px-3 py-2 text-sm text-gray-900 dark:text-gray-100 hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-colors">
                            <span x-text="mod.pretty_name"></span>
                            <span x-show="mod.pretty_name !== mod.name"
                                  class="text-xs text-gray-400 ml-1" x-text="'(' + mod.name + ')'"></span>
                        </button>
                    </template>
                </div>
            </div>

            {{-- Mod version select --}}
            <div class="flex-1 w-full sm:w-auto">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Mod version</label>
                <select x-model="selectedVersion"
                        :disabled="versions.length === 0"
                        class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                    <template x-if="versions.length === 0">
                        <option value="">Select a mod first...</option>
                    </template>
                    <template x-for="v in versions" :key="v">
                        <option :value="v" x-text="v"></option>
                    </template>
                </select>
            </div>

            {{-- Add button --}}
            <div class="shrink-0">
                <button type="button"
                        @click="addToBuild()"
                        :disabled="!selectedVersion || loadingVersions"
                        class="bg-blue-600 hover:bg-blue-700 text-white dark:bg-blue-500/15 dark:text-blue-400 dark:hover:bg-blue-500/25 font-medium py-2 px-4 rounded-lg text-sm transition-colors disabled:opacity-50 disabled:cursor-not-allowed whitespace-nowrap">
                    <span x-show="!loadingVersions">Add to build</span>
                    <span x-show="loadingVersions">Loading...</span>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Mod list card --}}
<div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-800"
     x-data="{
         addedMods: [],
         init() {
             this.$el.addEventListener('mod-added', (e) => {
                 this.addedMods.unshift(e.detail);
             });
         }
     }"
     @mod-added.window="addedMods.unshift($event.detail)">
    <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-800">
        <span class="font-semibold text-gray-900 dark:text-white">Mod List</span>
    </div>
    <div class="divide-y divide-gray-100 dark:divide-gray-800 text-sm">
        {{-- Dynamically added mods (shown at top) --}}
        <template x-for="(mod, index) in addedMods" :key="'added-' + index">
            <div class="flex flex-col sm:flex-row sm:items-center gap-2 px-5 py-3 bg-green-50/50 dark:bg-green-900/10">
                <span class="text-gray-900 dark:text-gray-100 sm:w-1/3" x-text="mod.pretty_name"></span>
                <div class="flex items-center gap-2">
                    <span class="text-gray-600 dark:text-gray-400" x-text="mod.version"></span>
                    <span class="text-xs text-green-600 dark:text-green-400">Just added</span>
                </div>
            </div>
        </template>

        {{-- Server-rendered mod rows --}}
        @foreach ($build->modversions->sortBy(fn($v) => strtolower($v->mod->pretty_name ?: $v->mod->name)) as $ver)
            <div x-data="{ removed: false, changing: false }" x-show="!removed"
                 class="flex flex-col sm:flex-row sm:items-center gap-2 px-5 py-3">
                <div class="sm:w-1/3 shrink-0">
                    <a href="{{ url('/mod/view/'.$ver->mod->id) }}"
                       class="text-blue-600 dark:text-blue-400 hover:underline font-medium">{{ $ver->mod->pretty_name ?: $ver->mod->name }}</a>
                    @if ($ver->mod->pretty_name && $ver->mod->pretty_name !== $ver->mod->name)
                        <span class="text-xs text-gray-500 dark:text-gray-400">({{ $ver->mod->name }})</span>
                    @endif
                </div>
                <div class="flex-1 min-w-0 space-y-2 sm:space-y-0 sm:flex sm:items-center sm:gap-2">
                    <select x-ref="versionSelect"
                            class="w-full sm:w-auto sm:min-w-0 sm:flex-1 px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors">
                        @foreach ($ver->mod->versions as $version)
                            <option value="{{ $version->id }}"
                                    @selected($ver->version == $version->version)
                            >{{ $version->version }}</option>
                        @endforeach
                    </select>
                    <div class="flex items-center gap-2 shrink-0">
                        <button type="button"
                                :disabled="changing"
                                @click="
                                    changing = true;
                                    const params = new URLSearchParams();
                                    params.append('_token', window.csrfToken);
                                    params.append('build_id', '{{ $build->id }}');
                                    params.append('modversion_id', '{{ $ver->pivot->modversion_id }}');
                                    params.append('action', 'version');
                                    params.append('version', $refs.versionSelect.value);
                                    fetch('{{ url('modpack/modify/version') }}', {
                                        method: 'POST',
                                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                                        body: params,
                                    })
                                    .then(r => r.json())
                                    .then(data => {
                                        if (data.status === 'success') {
                                            Alpine.store('toasts').add('Mod version updated', 'success');
                                        } else if (data.status === 'aborted') {
                                            Alpine.store('toasts').add('Mod was already set to that version', 'success');
                                        } else {
                                            Alpine.store('toasts').add('Unable to update mod version', 'warning');
                                        }
                                    })
                                    .catch(() => Alpine.store('toasts').add('Failed to update mod version', 'error'))
                                    .finally(() => changing = false);
                                "
                                class="bg-blue-600 hover:bg-blue-700 text-white dark:bg-blue-500/15 dark:text-blue-400 dark:hover:bg-blue-500/25 font-medium py-1.5 px-3 text-xs rounded-lg transition-colors whitespace-nowrap disabled:opacity-50">
                            Change
                        </button>
                        <button type="button"
                                @click="
                                    const params = new URLSearchParams();
                                    params.append('_token', window.csrfToken);
                                    params.append('build_id', '{{ $build->id }}');
                                    params.append('modversion_id', '{{ $ver->pivot->modversion_id }}');
                                    params.append('action', 'delete');
                                    fetch('{{ url('modpack/modify/delete') }}', {
                                        method: 'POST',
                                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                                        body: params,
                                    })
                                    .then(r => r.json())
                                    .then(data => {
                                        if (data.status === 'success') {
                                            Alpine.store('toasts').add('Mod version removed', 'success');
                                            removed = true;
                                        } else {
                                            Alpine.store('toasts').add('Unable to remove mod version', 'warning');
                                        }
                                    })
                                    .catch(() => Alpine.store('toasts').add('Failed to remove mod version', 'error'));
                                "
                                class="bg-red-600 hover:bg-red-700 text-white dark:bg-red-500/15 dark:text-red-400 dark:hover:bg-red-500/25 font-medium py-1.5 px-3 text-xs rounded-lg transition-colors whitespace-nowrap">
                            Remove
                        </button>
                    </div>
                </div>
            </div>
        @endforeach

        @if ($build->modversions->isEmpty())
            <div class="px-5 py-8 text-center text-gray-500 dark:text-gray-400">
                No mods have been added to this build yet.
            </div>
        @endif
    </div>
</div>
