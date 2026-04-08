@extends('layouts.master')
@section('title')
    <title>{{ $mod->pretty_name ?: $mod->name }} - Technic Solder</title>
@stop
@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold">Mod Library</h1>
    </div>

    <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-800"
         x-data="modView()">
        <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-800">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                @if (!empty($mod->pretty_name))
                    {{ $mod->pretty_name }}
                    <span class="text-gray-500 dark:text-gray-400 font-normal">{{ $mod->name }}</span>
                @else
                    {{ $mod->name }}
                @endif
            </h2>
        </div>
        <div class="p-5">
            {{-- Tabs --}}
            <div class="flex gap-1 mb-6 border-b border-gray-200 dark:border-gray-800">
                <button @click="tab = 'versions'"
                        :class="tab === 'versions'
                            ? 'border-blue-600 text-blue-600 dark:border-blue-400 dark:text-blue-400'
                            : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600'"
                        class="px-4 py-2.5 text-sm font-medium border-b-2 -mb-px transition-colors">
                    Versions
                </button>
                <button @click="tab = 'details'"
                        :class="tab === 'details'
                            ? 'border-blue-600 text-blue-600 dark:border-blue-400 dark:text-blue-400'
                            : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600'"
                        class="px-4 py-2.5 text-sm font-medium border-b-2 -mb-px transition-colors">
                    Details
                </button>
            </div>

            {{-- Versions Tab --}}
            <div x-show="tab === 'versions'">
                <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 mb-5">
                    <p class="text-sm text-gray-700 dark:text-gray-300 mb-2">
                        Solder currently does not support uploading files directly to it. Your repository still needs to
                        exist and follow a strict directory structure. When you add versions the URL will be verified to
                        make sure the file exists before it is added to Solder.
                    </p>
                    <p class="text-sm font-mono text-gray-800 dark:text-gray-200">
                        mods/<span class="text-blue-600 dark:text-blue-400">{{ $mod->name }}</span>/<span class="text-blue-600 dark:text-blue-400">{{ $mod->name }}</span>-[version].zip
                    </p>
                </div>

                <div class="flex items-center justify-between gap-3">
                    <div class="flex-1">
                        @include('partial.data-table.toolbar', ['placeholder' => 'Search versions...', 'showPageSize' => false])
                    </div>
                    <button @click="rehashAllRunning ? rehashAllAborted = true : rehashAll()"
                            :disabled="rows.length === 0"
                            class="bg-blue-600 hover:bg-blue-700 text-white dark:bg-blue-500/15 dark:text-blue-400 dark:hover:bg-blue-500/25 font-medium py-2 px-4 text-xs rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed whitespace-nowrap">
                        <span x-show="!rehashAllRunning">Rehash All</span>
                        <span x-show="rehashAllRunning && !rehashAllAborted" x-text="'Rehashing ' + rehashAllCurrent + '/' + rehashAllTotal + '... (click to cancel)'"></span>
                        <span x-show="rehashAllAborted">Cancelling...</span>
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-800/50 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                <th class="px-5 py-3 w-8"></th>
                                @include('partial.data-table.sort-header', ['key' => 'version', 'label' => 'Version'])
                                <th class="px-5 py-3" style="width: 25%">MD5</th>
                                <th class="px-5 py-3" style="width: 30%">Download URL</th>
                                <th class="px-5 py-3" style="width: 10%">Filesize</th>
                                <th class="px-5 py-3" style="width: 15%"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                            {{-- Add version row (pinned) --}}
                            <tr class="bg-blue-50/50 dark:bg-blue-900/10">
                                <td class="px-5 py-3"></td>
                                <td class="px-5 py-3">
                                    <input type="text"
                                           x-model="addVersion"
                                           placeholder="Version"
                                           class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors">
                                </td>
                                <td class="px-5 py-3">
                                    <input type="text"
                                           x-model="addMd5"
                                           placeholder="MD5 (optional)"
                                           class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors">
                                </td>
                                <td class="px-5 py-3">
                                    <template x-if="addVersion">
                                        <a :href="addUrl" target="_blank" class="text-blue-600 dark:text-blue-400 hover:underline text-xs break-all" x-text="addUrl"></a>
                                    </template>
                                    <template x-if="!addVersion">
                                        <span class="text-gray-400 dark:text-gray-500 text-xs">N/A</span>
                                    </template>
                                </td>
                                <td class="px-5 py-3 text-gray-400 dark:text-gray-500 text-xs">N/A</td>
                                <td class="px-5 py-3">
                                    <button @click="submitAddVersion()"
                                            :disabled="addLoading || !addVersion"
                                            class="bg-blue-600 hover:bg-blue-700 text-white dark:bg-blue-500/15 dark:text-blue-400 dark:hover:bg-blue-500/25 font-medium py-1.5 px-3 text-xs rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                        <span x-show="!addLoading">Add Version</span>
                                        <span x-show="addLoading">Adding...</span>
                                    </button>
                                </td>
                            </tr>
                        </tbody>

                        {{-- Data-driven version rows with inline expand --}}
                        <template x-for="row in paged" :key="row.id">
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                                    <tr>
                                        <td class="px-5 py-3">
                                            <button @click="toggleExpand(row.id)"
                                                    type="button"
                                                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                                                <svg class="size-4 transition-transform"
                                                     :class="expandedVersions.includes(row.id) && 'rotate-90'"
                                                     fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/>
                                                </svg>
                                            </button>
                                        </td>
                                        <td class="px-5 py-3 font-medium text-gray-900 dark:text-gray-100" x-text="row.version"></td>
                                        <td class="px-5 py-3">
                                            <input type="text"
                                                   :id="'md5-' + row.id"
                                                   :placeholder="row.md5"
                                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors">
                                        </td>
                                        <td class="px-5 py-3">
                                            <a :href="row.url" target="_blank"
                                               class="text-blue-600 dark:text-blue-400 hover:underline text-xs break-all" x-text="row.url"></a>
                                        </td>
                                        <td class="px-5 py-3 text-gray-700 dark:text-gray-300" x-text="row.filesize"></td>
                                        <td class="px-5 py-3">
                                            <div class="flex items-center gap-2">
                                                <button @click="rehashVersion(row.id)"
                                                        :disabled="rehashingVersions.includes(row.id)"
                                                        class="bg-blue-600 hover:bg-blue-700 text-white dark:bg-blue-500/15 dark:text-blue-400 dark:hover:bg-blue-500/25 font-medium py-1.5 px-3 text-xs rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                                    <span x-show="!rehashingVersions.includes(row.id)">Rehash</span>
                                                    <span x-show="rehashingVersions.includes(row.id)">...</span>
                                                </button>
                                                <button @click="deleteVersion(row.id)"
                                                        :disabled="deletingVersions.includes(row.id)"
                                                        class="bg-red-600 hover:bg-red-700 text-white dark:bg-red-500/15 dark:text-red-400 dark:hover:bg-red-500/25 font-medium py-1.5 px-3 text-xs rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                                    Delete
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr x-show="expandedVersions.includes(row.id)">
                                        <td colspan="6" class="px-5 py-3 bg-gray-50 dark:bg-gray-800/30">
                                            <template x-if="row.builds.length === 0 && row.hiddenCount === 0">
                                                <p class="text-sm text-gray-500 dark:text-gray-400">Not used in any builds</p>
                                            </template>
                                            <template x-if="row.builds.length === 0 && row.hiddenCount > 0">
                                                <p class="text-sm text-gray-500 dark:text-gray-400"
                                                   x-text="'Used in ' + row.hiddenCount + ' build' + (row.hiddenCount === 1 ? '' : 's') + ' you don\u2019t have access to'"></p>
                                            </template>
                                            <template x-if="row.builds.length > 0">
                                                <div>
                                                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Builds used in:</p>
                                                    <ul class="space-y-1 ml-4">
                                                        <template x-for="build in row.builds" :key="build.id">
                                                            <li class="text-sm text-gray-600 dark:text-gray-400">
                                                                <a :href="'/modpack/view/' + build.modpack_id"
                                                                   class="text-blue-600 dark:text-blue-400 hover:underline" x-text="build.modpack_name"></a>
                                                                -
                                                                <a :href="'/modpack/build/' + build.id"
                                                                   class="text-blue-600 dark:text-blue-400 hover:underline" x-text="build.version"></a>
                                                            </li>
                                                        </template>
                                                        <template x-if="row.hiddenCount > 0">
                                                            <li class="text-sm text-gray-400 dark:text-gray-500 italic"
                                                                x-text="'+ ' + row.hiddenCount + ' other' + (row.hiddenCount === 1 ? '' : 's')"></li>
                                                        </template>
                                                    </ul>
                                                </div>
                                            </template>
                                        </td>
                                    </tr>
                            </tbody>
                        </template>
                    </table>
                </div>
            </div>

            {{-- Details Tab --}}
            <div x-show="tab === 'details'" x-cloak>
                @include('partial.form-errors')
                @session('success')
                    <div class="mb-4 p-4 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-sm text-green-700 dark:text-green-300">
                        {{ $value }}
                    </div>
                @endsession

                <form method="post"
                      action="{{ url('/mod/modify/'.$mod->id) }}"
                      accept-charset="UTF-8"
                      x-data="{
                          prettyName: @js($mod->pretty_name),
                          slug: @js($mod->name),
                          slugManual: true,
                          updateSlug() {
                              if (!this.slugManual) {
                                  this.slug = window.slugify(this.prettyName);
                              }
                          },
                          onSlugInput() {
                              if (this.slug === '') {
                                  this.slugManual = false;
                                  this.updateSlug();
                              } else {
                                  this.slugManual = true;
                              }
                          },
                          get slugPreview() {
                              return this.slug || '[slug]';
                          }
                      }">
                    @csrf
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <div class="space-y-4">
                        <div>
                            <label for="pretty_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Pretty Name</label>
                            <input type="text"
                                   name="pretty_name"
                                   id="pretty_name"
                                   x-model="prettyName"
                                   @input="updateSlug()"
                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors">
                        </div>
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Slug</label>
                            <input type="text"
                                   name="name"
                                   id="name"
                                   x-model="slug"
                                   @input="onSlugInput()"
                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors">
                        </div>
                        <div>
                            <label for="author" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Author</label>
                            <input type="text"
                                   name="author"
                                   id="author"
                                   value="{{ $mod->author }}"
                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors">
                        </div>
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                            <textarea name="description"
                                      id="description"
                                      rows="5"
                                      class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors">{{ $mod->description }}</textarea>
                        </div>
                        <div>
                            <label for="link" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Website</label>
                            <input type="text"
                                   name="link"
                                   id="link"
                                   value="{{ $mod->link }}"
                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors">
                        </div>
                    </div>
                    <div>
                        <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700">
                            <p class="text-sm text-gray-700 dark:text-gray-300 mb-3">
                                Your mod directory structure must match the slug exactly:
                            </p>
                            <div class="bg-gray-100 dark:bg-gray-800 rounded-lg p-3 font-mono text-sm text-gray-800 dark:text-gray-200 mb-3">
                                <div>mods/<span x-text="slugPreview" class="text-blue-600 dark:text-blue-400"></span>/</div>
                                <div>mods/<span x-text="slugPreview" class="text-blue-600 dark:text-blue-400"></span>/<span x-text="slugPreview" class="text-blue-600 dark:text-blue-400"></span>-[version].zip</div>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                The mod slug automatically updates based on the mod name. You can change the slug to whatever
                                you want after you set the name. If you modify the slug, it will no longer update
                                automatically. If you wish to restore that behavior, simply empty the slug field.
                            </p>
                        </div>
                    </div>
                    </div>
                    <div class="mt-6 flex items-center gap-3">
                        <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white dark:bg-blue-500/15 dark:text-blue-400 dark:hover:bg-blue-500/25 font-medium py-2 px-4 rounded-lg text-sm transition-colors">
                            Save Changes
                        </button>
                        <a href="{{ url('/mod/delete/'.$mod->id) }}"
                           class="bg-red-600 hover:bg-red-700 text-white dark:bg-red-500/15 dark:text-red-400 dark:hover:bg-red-500/25 font-medium py-2 px-4 rounded-lg text-sm transition-colors">
                            Delete Mod
                        </a>
                        <a href="{{ url('/mod/list') }}"
                           class="bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 font-medium py-2 px-4 rounded-lg text-sm transition-colors">
                            Go Back
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        function modView() {
            return {
                // --- dataTable properties ---
                rows: @js($mod->versions->map(function($v) use ($mod, $accessibleModpackIds) {
                    $builds = $v->builds->map(fn($b) => [
                        'id' => $b->id,
                        'version' => $b->version,
                        'modpack_id' => $b->modpack->id,
                        'modpack_name' => $b->modpack->name,
                        'accessible' => $accessibleModpackIds === null || in_array($b->modpack->id, $accessibleModpackIds),
                    ]);
                    return [
                        'id' => $v->id,
                        'version' => $v->version,
                        'md5' => $v->md5,
                        'filesize' => $v->humanFilesize(),
                        'url' => config('solder.mirror_url') . 'mods/' . $mod->name . '/' . $mod->name . '-' . $v->version . '.zip',
                        'builds' => $builds->where('accessible', true)->values(),
                        'hiddenCount' => $builds->where('accessible', false)->count(),
                    ];
                })),
                sortKey: 'version',
                sortDir: 'desc',
                search: '',
                page: 1,
                pageSize: 25,
                paginate: false,
                types: {},
                searchKeys: ['version', 'md5'],

                init() {
                    this.$watch('search', () => { this.page = 1; });
                },

                // --- dataTable computed getters ---
                sort(key) {
                    if (this.sortKey === key) {
                        this.sortDir = this.sortDir === 'asc' ? 'desc' : 'asc';
                    } else {
                        this.sortKey = key;
                        this.sortDir = 'asc';
                    }
                    this.page = 1;
                },

                get filtered() {
                    if (!this.search) return this.rows;
                    const s = this.search.toLowerCase();
                    const keys = this.searchKeys || Object.keys(this.rows[0] || {});
                    return this.rows.filter(row =>
                        keys.some(k => String(row[k] ?? '').toLowerCase().includes(s))
                    );
                },

                get sorted() {
                    const key = this.sortKey;
                    if (!key) return this.filtered;
                    const dir = this.sortDir === 'asc' ? 1 : -1;
                    const type = this.types[key] || 'string';
                    return [...this.filtered].sort((a, b) => {
                        let va = a[key] ?? '', vb = b[key] ?? '';
                        if (type === 'number') return ((parseFloat(va) || 0) - (parseFloat(vb) || 0)) * dir;
                        if (type === 'date') return ((new Date(va).getTime() || 0) - (new Date(vb).getTime() || 0)) * dir;
                        return dir * String(va).localeCompare(String(vb), undefined, { numeric: true, sensitivity: 'base' });
                    });
                },

                get paged() {
                    if (!this.paginate) return this.sorted;
                    const start = (this.page - 1) * this.pageSize;
                    return this.sorted.slice(start, start + this.pageSize);
                },

                get totalFiltered() { return this.filtered.length; },
                get totalRows() { return this.rows.length; },
                get totalPages() { return this.paginate ? Math.max(1, Math.ceil(this.totalFiltered / this.pageSize)) : 1; },
                get showingFrom() { return this.totalFiltered === 0 ? 0 : (this.page - 1) * this.pageSize + 1; },
                get showingTo() { return Math.min(this.page * this.pageSize, this.totalFiltered); },
                get pageNumbers() {
                    const total = this.totalPages;
                    const current = this.page;
                    const pages = [];
                    if (total <= 7) {
                        for (let i = 1; i <= total; i++) pages.push(i);
                    } else {
                        pages.push(1);
                        if (current > 3) pages.push('...');
                        for (let i = Math.max(2, current - 1); i <= Math.min(total - 1, current + 1); i++) {
                            pages.push(i);
                        }
                        if (current < total - 2) pages.push('...');
                        pages.push(total);
                    }
                    return pages;
                },

                goToPage(n) { this.page = n; },
                prevPage() { if (this.page > 1) this.page--; },
                nextPage() { if (this.page < this.totalPages) this.page++; },

                // --- modView properties ---
                tab: 'versions',
                addVersion: '',
                addMd5: '',
                addLoading: false,
                expandedVersions: [],
                rehashingVersions: [],
                deletingVersions: [],
                rehashAllRunning: false,
                rehashAllCurrent: 0,
                rehashAllTotal: 0,
                rehashAllAborted: false,
                mirrorUrl: @js(config('solder.mirror_url')),
                modName: @js($mod->name),
                modId: @js($mod->id),

                get addUrl() {
                    return this.mirrorUrl + 'mods/' + this.modName + '/' + this.modName + '-' + this.addVersion + '.zip';
                },

                toggleExpand(verId) {
                    const idx = this.expandedVersions.indexOf(verId);
                    if (idx === -1) {
                        this.expandedVersions.push(verId);
                    } else {
                        this.expandedVersions.splice(idx, 1);
                    }
                },

                async submitAddVersion() {
                    if (!this.addVersion) return;
                    this.addLoading = true;

                    try {
                        const res = await fetch('{{ url("mod/add-version") }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': window.csrfToken,
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                            body: new URLSearchParams({
                                'mod-id': this.modId,
                                'add-version': this.addVersion,
                                'add-md5': this.addMd5,
                            }),
                        });
                        const data = await res.json();

                        if (data.status === 'success' || data.status === 'warning') {
                            this.rows.unshift({
                                id: data.version_id,
                                version: data.version,
                                md5: data.md5,
                                filesize: data.filesize,
                                url: this.mirrorUrl + 'mods/' + this.modName + '/' + this.modName + '-' + data.version + '.zip',
                                builds: [],
                            });
                            const msg = data.status === 'warning'
                                ? 'Added mod version at ' + data.version + '. ' + data.reason
                                : 'Added mod version at ' + data.version;
                            Alpine.store('toasts').add(msg, data.status);
                            this.addVersion = '';
                            this.addMd5 = '';
                        } else {
                            Alpine.store('toasts').add('Error: ' + data.reason, 'error');
                        }
                    } catch (err) {
                        Alpine.store('toasts').add('Request failed: ' + err.message, 'error');
                    } finally {
                        this.addLoading = false;
                    }
                },

                async rehashVersion(verId) {
                    this.rehashingVersions.push(verId);
                    const md5Input = document.getElementById('md5-' + verId);
                    const md5Value = md5Input ? md5Input.value : '';

                    try {
                        const res = await fetch('{{ url("mod/rehash") }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': window.csrfToken,
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                            body: new URLSearchParams({
                                'version-id': verId,
                                'md5': md5Value,
                            }),
                        });
                        const data = await res.json();

                        if (data.status === 'success') {
                            Alpine.store('toasts').add('MD5 hashing complete.', 'success');
                        } else if (data.status === 'warning') {
                            Alpine.store('toasts').add('MD5 hashing complete. ' + data.reason, 'warning');
                        } else {
                            Alpine.store('toasts').add('Error: ' + data.reason, 'error');
                        }

                        if (data.md5) {
                            const row = this.rows.find(r => r.id === verId);
                            if (row) row.md5 = data.md5;
                            if (md5Input) {
                                md5Input.value = '';
                                md5Input.placeholder = data.md5;
                            }
                        }
                        if (data.filesize) {
                            const row = this.rows.find(r => r.id === verId);
                            if (row) row.filesize = data.filesize;
                        }
                    } catch (err) {
                        Alpine.store('toasts').add('Request failed: ' + err.message, 'error');
                    } finally {
                        this.rehashingVersions = this.rehashingVersions.filter(id => id !== verId);
                    }
                },

                async rehashAll() {
                    if (this.rows.length === 0) return;

                    this.rehashAllRunning = true;
                    this.rehashAllAborted = false;
                    this.rehashAllTotal = this.rows.length;
                    this.rehashAllCurrent = 0;

                    let successCount = 0, warningCount = 0, errorCount = 0;

                    for (const row of this.rows) {
                        if (this.rehashAllAborted) break;
                        this.rehashAllCurrent++;
                        this.rehashingVersions.push(row.id);

                        try {
                            const params = new URLSearchParams();
                            params.append('version-id', row.id);
                            params.append('md5', '');

                            const res = await fetch('{{ url("mod/rehash") }}', {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': window.csrfToken,
                                    'X-Requested-With': 'XMLHttpRequest',
                                },
                                body: params,
                            });
                            const data = await res.json();

                            if (data.status === 'success') successCount++;
                            else if (data.status === 'warning') warningCount++;
                            else errorCount++;

                            if (data.md5) {
                                row.md5 = data.md5;
                                const md5Input = document.getElementById('md5-' + row.id);
                                if (md5Input) { md5Input.value = ''; md5Input.placeholder = data.md5; }
                            }
                            if (data.filesize) row.filesize = data.filesize;
                        } catch (err) {
                            errorCount++;
                        } finally {
                            this.rehashingVersions = this.rehashingVersions.filter(id => id !== row.id);
                        }
                    }

                    this.rehashAllRunning = false;
                    const parts = [];
                    if (successCount > 0) parts.push(successCount + ' success');
                    if (warningCount > 0) parts.push(warningCount + ' warning');
                    if (errorCount > 0) parts.push(errorCount + ' error');
                    if (this.rehashAllAborted) parts.push('aborted');
                    const type = errorCount > 0 ? 'error' : (warningCount > 0 ? 'warning' : 'success');
                    Alpine.store('toasts').add('Rehash complete: ' + parts.join(', '), type);
                },

                async deleteVersion(verId) {
                    this.deletingVersions.push(verId);

                    try {
                        const res = await fetch('{{ url("mod/delete-version") }}/' + verId, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': window.csrfToken,
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                            body: new URLSearchParams({}),
                        });
                        const data = await res.json();

                        if (data.status === 'success') {
                            this.rows = this.rows.filter(r => r.id !== data.version_id);
                            Alpine.store('toasts').add('Mod version ' + data.version + ' deleted.', 'success');
                        } else {
                            Alpine.store('toasts').add('Error: ' + data.reason, 'error');
                        }
                    } catch (err) {
                        Alpine.store('toasts').add('Request failed: ' + err.message, 'error');
                    } finally {
                        this.deletingVersions = this.deletingVersions.filter(id => id !== verId);
                    }
                },
            };
        }
    </script>
@endpush
