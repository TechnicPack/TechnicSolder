{{-- Add a mod card --}}
<div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-800 mb-6">
    <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-800">
        <span class="font-semibold text-gray-900 dark:text-white">Add a mod</span>
    </div>
    <div class="p-5" x-data="modSearch()" @mod-removed.window="modsInBuild.delete($event.detail.mod_name)">
        <div class="flex flex-col sm:flex-row gap-3 items-end">
            {{-- Mod name searchable select --}}
            <div class="flex-1 w-full sm:w-auto relative">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Mod name</label>
                <input type="text"
                       x-ref="modInput"
                       x-model="query"
                       @focus="showDropdown = true"
                       @input="showDropdown = true; modHighlight = -1; if (!query) clearSelection()"
                       @click.outside="showDropdown = false; modHighlight = -1"
                       @keydown.escape="showDropdown = false; modHighlight = -1"
                       @keydown.arrow-down.prevent="modArrow('down')"
                       @keydown.arrow-up.prevent="modArrow('up')"
                       @keydown.enter.prevent="if (modHighlight >= 0 && filteredMods[modHighlight]) selectMod(filteredMods[modHighlight])"
                       placeholder="Search for a mod..."
                       autocomplete="off"
                       class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors">
                {{-- Dropdown --}}
                <div x-show="showDropdown && filteredMods.length > 0"
                     x-ref="modDropdown"
                     x-transition
                     class="absolute z-20 mt-1 w-full max-h-60 overflow-y-auto bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg"
                     style="display: none">
                    <template x-for="(mod, i) in filteredMods" :key="mod.name">
                        <button type="button" tabindex="-1"
                                @click="selectMod(mod)"
                                @mouseenter="modHighlight = i"
                                class="w-full text-left px-3 py-2 text-sm transition-colors"
                                :class="i === modHighlight
                                    ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300'
                                    : 'text-gray-900 dark:text-gray-100 hover:bg-blue-50 dark:hover:bg-blue-900/30'">
                            <span x-text="mod.pretty_name"></span>
                            <span x-show="mod.pretty_name !== mod.name"
                                  class="text-xs text-gray-400 ml-1" x-text="'(' + mod.name + ')'"></span>
                        </button>
                    </template>
                </div>
            </div>

            {{-- Mod version searchable select --}}
            <div class="flex-1 w-full sm:w-auto relative" @click.outside="showVersionDropdown = false; versionHighlight = -1; versionQuery = selectedVersion">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Mod version</label>
                <input type="text"
                       x-ref="versionInput"
                       x-model="versionQuery"
                       @focus="versionQuery = ''; showVersionDropdown = true; versionHighlight = -1"
                       @click="showVersionDropdown = true"
                       @input="showVersionDropdown = true; versionHighlight = -1"
                       @keydown.escape="showVersionDropdown = false; versionHighlight = -1; versionQuery = selectedVersion"
                       @keydown.arrow-down.prevent="versionArrow('down')"
                       @keydown.arrow-up.prevent="versionArrow('up')"
                       @keydown.enter.prevent="if (showVersionDropdown && versionHighlight >= 0) { selectVersion(filteredVersions[versionHighlight]); } else if (showVersionDropdown && versionQuery && filteredVersions.length) { selectVersion(filteredVersions[0]); } else if (selectedVersion) { addToBuild().then(ok => ok && $refs.modInput?.focus()); }"
                       :placeholder="versions.length === 0 ? 'Select a mod first...' : 'Search versions...'"
                       :disabled="versions.length === 0"
                       autocomplete="off"
                       class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                {{-- Dropdown --}}
                <div x-show="showVersionDropdown && filteredVersions.length > 0"
                     x-ref="versionDropdown"
                     x-transition
                     class="absolute z-20 mt-1 w-full max-h-60 overflow-y-auto bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg"
                     style="display: none">
                    <template x-for="(v, i) in filteredVersions" :key="v">
                        <button type="button" tabindex="-1"
                                @click="selectVersion(v)"
                                @mouseenter="versionHighlight = i"
                                class="w-full text-left px-3 py-2 text-sm transition-colors"
                                :class="i === versionHighlight
                                    ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 font-medium'
                                    : 'text-gray-900 dark:text-gray-100 hover:bg-blue-50 dark:hover:bg-blue-900/30'">
                            <span x-text="v"></span>
                        </button>
                    </template>
                </div>
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
     x-data="modList()"
     @mod-added.window="addMod($event.detail)">
    <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-800 flex items-center justify-between">
        <span class="font-semibold text-gray-900 dark:text-white">Mod List</span>
        <button type="button"
                x-show="pendingMods.length > 0"
                x-cloak
                :disabled="savingAll"
                @click="saveAll()"
                class="bg-blue-600 hover:bg-blue-700 text-white dark:bg-blue-500/15 dark:text-blue-400 dark:hover:bg-blue-500/25 font-medium py-1.5 px-3 text-xs rounded-lg transition-colors whitespace-nowrap disabled:opacity-50">
            <span x-show="!savingAll">Save All (<span x-text="pendingMods.length"></span>)</span>
            <span x-show="savingAll">Saving...</span>
        </button>
    </div>
    <div class="divide-y divide-gray-100 dark:divide-gray-800 text-sm">
        <template x-for="mod in mods" :key="mod.modversion_id">
            <div :class="mod.just_added ? 'bg-green-50/50 dark:bg-green-900/10' : ''"
                 class="flex flex-col sm:flex-row sm:items-center gap-2 px-5 py-3">
                <div class="sm:w-1/3 shrink-0">
                    <a :href="'/mod/view/' + mod.mod_id"
                       class="text-blue-600 dark:text-blue-400 hover:underline font-medium" x-text="mod.pretty_name"></a>
                    <template x-if="mod.pretty_name !== mod.mod_name">
                        <span class="text-xs text-gray-500 dark:text-gray-400" x-text="'(' + mod.mod_name + ')'"></span>
                    </template>
                    <span x-show="mod.just_added" class="text-xs text-green-600 dark:text-green-400 ml-1">Just added</span>
                </div>
                <div class="flex-1 min-w-0 space-y-2 sm:space-y-0 sm:flex sm:items-center sm:gap-2">
                    <select x-model="mod.selected_version_id"
                            class="w-full sm:w-auto sm:min-w-0 sm:flex-1 px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors">
                        <template x-for="v in mod.versions" :key="v.id">
                            <option :value="String(v.id)" :selected="String(v.id) === mod.selected_version_id" x-text="v.version"></option>
                        </template>
                    </select>
                    <div class="flex items-center gap-2 shrink-0">
                        <button type="button"
                                :disabled="mod.changing"
                                @click="changeVersion(mod)"
                                class="bg-blue-600 hover:bg-blue-700 text-white dark:bg-blue-500/15 dark:text-blue-400 dark:hover:bg-blue-500/25 font-medium py-1.5 px-3 text-xs rounded-lg transition-colors whitespace-nowrap disabled:opacity-50">
                            Change
                        </button>
                        <button type="button"
                                @click="removeMod(mod)"
                                class="bg-red-600 hover:bg-red-700 text-white dark:bg-red-500/15 dark:text-red-400 dark:hover:bg-red-500/25 font-medium py-1.5 px-3 text-xs rounded-lg transition-colors whitespace-nowrap">
                            Remove
                        </button>
                    </div>
                </div>
            </div>
        </template>

        <div x-show="mods.length === 0" class="px-5 py-8 text-center text-gray-500 dark:text-gray-400">
            No mods have been added to this build yet.
        </div>
    </div>
</div>
