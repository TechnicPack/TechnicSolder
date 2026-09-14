{{-- Add a mod card --}}
<div class="ui-card mb-6">
    <div class="ui-card-header">
        <span class="font-semibold text-gray-900 dark:text-white">Add a mod</span>
    </div>
    <div class="p-5" x-data="modSearch()" @mod-removed.window="modsInBuild.delete($event.detail.mod_name)">
        <div class="flex flex-col sm:flex-row gap-3 items-end">
            {{-- Mod name searchable select --}}
            <div class="flex-1 w-full sm:w-auto relative">
                <label class="ui-label">Mod name</label>
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
                       class="ui-control">
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
                <label class="ui-label">Mod version</label>
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
                       class="ui-control disabled:opacity-50 disabled:cursor-not-allowed">
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
                        class="ui-btn ui-btn-primary disabled:opacity-50 disabled:cursor-not-allowed whitespace-nowrap">
                    <span x-show="!loadingVersions">Add to build</span>
                    <span x-show="loadingVersions">Loading...</span>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Mod list card --}}
<div class="ui-card"
     x-data="modList()"
     @mod-added.window="addMod($event.detail)">
    <div class="ui-card-header flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <span class="font-semibold text-gray-900 dark:text-white">Mod List</span>
        <div class="flex w-full items-center gap-2 sm:w-auto">
            <label for="mod-list-filter" class="sr-only">Filter mods</label>
            <input type="search"
                   id="mod-list-filter"
                   x-model.debounce.200ms="filter"
                   placeholder="Filter mods..."
                   class="ui-control w-auto min-w-0 flex-1 sm:w-64 sm:flex-none">
            <button type="button"
                    x-show="pendingMods.length > 0"
                    x-cloak
                    :disabled="savingAll"
                    @click="saveAll()"
                    class="ui-btn ui-btn-sm ui-btn-primary shrink-0 whitespace-nowrap disabled:opacity-50">
                <span x-show="!savingAll">Save All (<span x-text="pendingMods.length"></span>)</span>
                <span x-show="savingAll">Saving...</span>
            </button>
        </div>
    </div>
    <div class="divide-y divide-gray-100 dark:divide-gray-800 text-sm">
        <template x-for="mod in filteredMods" :key="mod.modversion_id">
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
                            class="ui-control sm:w-auto sm:min-w-0 sm:flex-1">
                        <template x-for="v in mod.versions" :key="v.id">
                            <option :value="String(v.id)" :selected="String(v.id) === mod.selected_version_id" x-text="v.version"></option>
                        </template>
                    </select>
                    <div class="flex items-center gap-2 shrink-0">
                        <button type="button"
                                :disabled="mod.changing"
                                @click="changeVersion(mod)"
                                class="ui-btn ui-btn-sm ui-btn-primary whitespace-nowrap disabled:opacity-50">
                            Change
                        </button>
                        <button type="button"
                                @click="removeMod(mod)"
                                class="ui-btn ui-btn-sm ui-btn-danger whitespace-nowrap">
                            Remove
                        </button>
                    </div>
                </div>
            </div>
        </template>

        <div x-show="mods.length === 0" x-cloak class="px-5 py-8 text-center text-gray-500 dark:text-gray-400">
            No mods have been added to this build yet.
        </div>
        <div x-show="mods.length > 0 && filteredMods.length === 0" x-cloak class="px-5 py-8 text-center text-gray-500 dark:text-gray-400">
            No mods match your filter.
        </div>
    </div>
</div>
