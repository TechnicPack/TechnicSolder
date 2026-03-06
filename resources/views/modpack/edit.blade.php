@extends('layouts.master')
@section('title')
    <title>{{ $modpack->name }} - Technic Solder</title>
@stop
@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Modpack Management - {{ $modpack->name }}</h1>
    </div>

    <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-800">
        <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-800">
            <span class="font-semibold text-gray-900 dark:text-white">Editing Modpack: {{ $modpack->name }}</span>
        </div>
        <div class="p-5">
            @session('success')
                <div class="mb-4 p-4 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-sm text-green-700 dark:text-green-300">
                    {{ $value }}
                </div>
            @endsession
            @include('partial.form-errors')
            <form method="post" action="{{ url()->current() }}" accept-charset="UTF-8"
                  x-data="{ name: @js($modpack->name), slug: @js($modpack->slug), showSlugWarning: false }">
                @csrf
                <div class="space-y-5">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Modpack Name</label>
                        <input type="text"
                               name="name"
                               id="name"
                               x-model="name"
                               @input="slug = window.slugify(name)"
                               class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors">
                    </div>
                    <div>
                        <label for="slug" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Modpack Slug</label>
                        <input type="text"
                               name="slug"
                               id="slug"
                               x-model="slug"
                               @focus="showSlugWarning = true"
                               class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors">
                    </div>
                    <div x-show="showSlugWarning" x-transition
                         class="p-4 rounded-lg bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 text-sm text-yellow-700 dark:text-yellow-400/80">
                        If you change the modpack slug you have to delete and re-import your pack on Technic Platform.
                    </div>

                    <hr class="border-gray-200 dark:border-gray-700">

                    <div>
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="checkbox"
                                   name="hidden"
                                   id="hidden"
                                   @checked($modpack->hidden)
                                   class="rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500 dark:bg-gray-800">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Hide Modpack</span>
                        </label>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Hidden modpacks will not show up in the API response for the modpack list. However, anyone with the modpack's slug can access all of its information.
                        </p>
                    </div>

                    <div>
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="checkbox"
                                   name="private"
                                   id="private"
                                   @checked($modpack->private)
                                   class="rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500 dark:bg-gray-800">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Private Modpack</span>
                        </label>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Private modpacks will only be available to clients that are linked to this modpack. You can link clients below. You can also individually mark builds as private.
                        </p>
                    </div>

                    <hr class="border-gray-200 dark:border-gray-700">

                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Modpack art (logo, icon, background) is managed in
                        <a href="https://www.technicpack.net/" target="_blank" rel="noopener" class="text-blue-600 dark:text-blue-400 hover:underline">Technic Platform</a>
                        under your modpack's "Resources" section.
                    </p>
                </div>

                @if ($modpack->private || $modpack->private_builds())
                    <hr class="my-6 border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Client Access</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 mb-4">Select which clients can access this modpack when it or its builds are set to private. Changes take effect when you save.</p>
                    @empty ($allClients)
                        <div class="p-4 rounded-lg bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 text-sm text-yellow-700 dark:text-yellow-400/80">
                            No clients to add
                        </div>
                    @else
                        <div x-data="{
                            options: @js($allClients->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)->map(fn($c) => ['id' => $c->id, 'name' => $c->name])->values()),
                            selected: @js($currentClients),
                            search: '',
                            open: false,
                            get filtered() {
                                const s = this.search.toLowerCase();
                                return this.options.filter(o => !s || o.name.toLowerCase().includes(s));
                            },
                            get selectedNames() {
                                return this.options.filter(o => this.selected.includes(o.id));
                            },
                            toggle(id) {
                                const idx = this.selected.indexOf(id);
                                if (idx === -1) this.selected.push(id);
                                else this.selected.splice(idx, 1);
                            },
                            remove(id) {
                                this.selected = this.selected.filter(s => s !== id);
                            },
                            selectAll() {
                                const ids = this.filtered.map(o => o.id);
                                ids.forEach(id => { if (!this.selected.includes(id)) this.selected.push(id); });
                            },
                            deselectAll() {
                                const ids = this.filtered.map(o => o.id);
                                this.selected = this.selected.filter(id => !ids.includes(id));
                            },
                        }">
                            {{-- Hidden inputs for form submission --}}
                            <template x-for="id in selected" :key="id">
                                <input type="hidden" name="clients[]" :value="id">
                            </template>

                            {{-- Selected pills --}}
                            <div class="flex flex-wrap gap-1.5 mb-3" x-show="selected.length > 0">
                                <template x-for="client in selectedNames" :key="client.id">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-500/20 text-blue-800 dark:text-blue-200 border border-transparent dark:border-blue-500/30">
                                        <span x-text="client.name"></span>
                                        <button type="button" @click="remove(client.id)" class="hover:text-blue-600 dark:hover:text-blue-100">&times;</button>
                                    </span>
                                </template>
                            </div>

                            {{-- Dropdown trigger --}}
                            <div class="relative" @click.outside="open = false">
                                <input type="text"
                                       x-model="search"
                                       @focus="open = true"
                                       @click="open = true"
                                       @keydown.escape="open = false"
                                       placeholder="Search clients to add..."
                                       autocomplete="off"
                                       class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors">

                                {{-- Dropdown --}}
                                <div x-show="open"
                                     x-transition
                                     class="absolute z-20 mt-1 w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg max-h-60 overflow-y-auto"
                                     style="display: none">
                                    <div class="sticky top-0 flex items-center gap-2 px-3 py-2 bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 text-xs">
                                        <button type="button" @click="selectAll()" class="text-blue-600 dark:text-blue-400 hover:underline">Select all</button>
                                        <span class="text-gray-300 dark:text-gray-600">|</span>
                                        <button type="button" @click="deselectAll()" class="text-blue-600 dark:text-blue-400 hover:underline">Deselect all</button>
                                        <span class="ml-auto text-gray-500 dark:text-gray-400" x-text="selected.length + ' selected'"></span>
                                    </div>
                                    <template x-for="option in filtered" :key="option.id">
                                        <button type="button"
                                                @click="toggle(option.id)"
                                                class="w-full text-left px-3 py-2 text-sm flex items-center gap-2 hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-colors"
                                                :class="selected.includes(option.id) ? 'text-blue-700 dark:text-blue-300' : 'text-gray-900 dark:text-gray-100'">
                                            <span class="w-4 h-4 shrink-0 rounded border flex items-center justify-center text-xs"
                                                  :class="selected.includes(option.id)
                                                      ? 'bg-blue-600 border-blue-600 text-white'
                                                      : 'border-gray-300 dark:border-gray-600'">
                                                <span x-show="selected.includes(option.id)">✓</span>
                                            </span>
                                            <span x-text="option.name"></span>
                                        </button>
                                    </template>
                                    <div x-show="filtered.length === 0" class="px-3 py-4 text-sm text-gray-500 dark:text-gray-400 text-center">
                                        No clients match your search
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endif

                <hr class="my-6 border-gray-200 dark:border-gray-700">

                <div class="flex items-center gap-3">
                    <button type="submit"
                            class="bg-green-600 hover:bg-green-700 text-white dark:bg-green-500/15 dark:text-green-400 dark:hover:bg-green-500/25 font-medium py-2 px-4 rounded-lg text-sm transition-colors">
                        Save Modpack
                    </button>
                    <a href="{{ url('/modpack/delete/'.$modpack->id) }}"
                       class="bg-red-600 hover:bg-red-700 text-white dark:bg-red-500/15 dark:text-red-400 dark:hover:bg-red-500/25 font-medium py-2 px-4 rounded-lg text-sm transition-colors">
                        Delete Modpack
                    </a>
                    <a href="{{ url('modpack/view/'.$modpack->id) }}"
                       class="bg-blue-600 hover:bg-blue-700 text-white dark:bg-blue-500/15 dark:text-blue-400 dark:hover:bg-blue-500/25 font-medium py-2 px-4 rounded-lg text-sm transition-colors">
                        Go Back
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
