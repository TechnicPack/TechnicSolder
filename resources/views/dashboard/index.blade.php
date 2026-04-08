@extends('layouts.master')
@section('title')
    <title>Dashboard - Technic Solder</title>
@stop
@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold">Dashboard</h1>
        <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Welcome to Technic Solder</p>
    </div>

    @session('permission')
        <div class="mb-4 p-4 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-sm text-red-700 dark:text-red-300">
            {{ $value }}
        </div>
    @endsession

    <div class="space-y-6">
        {{-- Recently Updated Modpacks --}}
        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-800" x-data="{ open: true }">
            <button @click="open = !open" class="w-full flex items-center justify-between px-5 py-4 text-left">
                <h2 class="font-semibold text-gray-900 dark:text-white">Recently Updated Modpacks</h2>
                <svg class="size-5 text-gray-400 transition-transform" :class="open && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>
            </button>
            <div x-show="open" x-collapse>
                <div class="overflow-x-auto border-t border-gray-200 dark:border-gray-800">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-800/50 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                <th class="px-5 py-3">#</th>
                                <th class="px-5 py-3">Build</th>
                                <th class="px-5 py-3">Modpack</th>
                                <th class="px-5 py-3">MC Version</th>
                                <th class="px-5 py-3">Mods</th>
                                <th class="px-5 py-3">Updated</th>
                                <th class="px-5 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                            @forelse ($builds as $build)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30">
                                    <td class="px-5 py-3 text-gray-500 dark:text-gray-400">{{ $build->id }}</td>
                                    <td class="px-5 py-3 font-medium">{{ $build->version }}</td>
                                    <td class="px-5 py-3">{{ $build->modpack->name }}</td>
                                    <td class="px-5 py-3">{{ $build->minecraft }}</td>
                                    <td class="px-5 py-3">{{ $build->modversions_count }}</td>
                                    <td class="px-5 py-3 text-gray-500 dark:text-gray-400">{{ $build->updated_at->diffForHumans() }}</td>
                                    <td class="px-5 py-3">
                                        <a href="{{ url('/modpack/build/'.$build->id) }}" class="text-blue-600 dark:text-blue-400 hover:underline text-sm">Manage</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-5 py-8 text-center text-sm text-gray-500 dark:text-gray-400">No recently updated modpacks.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Recently Added Mod Versions --}}
        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-800" x-data="{ open: true }">
            <button @click="open = !open" class="w-full flex items-center justify-between px-5 py-4 text-left">
                <h2 class="font-semibold text-gray-900 dark:text-white">Recently Added Mod Versions</h2>
                <svg class="size-5 text-gray-400 transition-transform" :class="open && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>
            </button>
            <div x-show="open" x-collapse>
                <div class="overflow-x-auto border-t border-gray-200 dark:border-gray-800">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-800/50 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                <th class="px-5 py-3">Version</th>
                                <th class="px-5 py-3">Mod</th>
                                <th class="px-5 py-3">Author</th>
                                <th class="px-5 py-3">Website</th>
                                <th class="px-5 py-3">Created</th>
                                <th class="px-5 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                            @forelse ($modversions as $modversion)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30">
                                    <td class="px-5 py-3 font-mono text-sm">{{ $modversion->version }}</td>
                                    <td class="px-5 py-3">
                                        <a href="{{ url('/mod/view/'.$modversion->mod->id) }}" class="text-blue-600 dark:text-blue-400 hover:underline">
                                            {{ $modversion->mod->pretty_name ?: $modversion->mod->name }}
                                        </a>
                                    </td>
                                    <td class="px-5 py-3 text-gray-500 dark:text-gray-400">{{ $modversion->mod->author ?: 'N/A' }}</td>
                                    <td class="px-5 py-3">
                                        @if ($modversion->mod->link)
                                            <a href="{{ $modversion->mod->link }}" target="_blank" rel="noopener noreferrer" class="text-blue-600 dark:text-blue-400 hover:underline truncate block max-w-48">{{ $modversion->mod->link }}</a>
                                        @else
                                            <span class="text-gray-400">N/A</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3 text-gray-500 dark:text-gray-400">{{ $modversion->created_at->diffForHumans() }}</td>
                                    <td class="px-5 py-3">
                                        <a href="{{ url('/mod/view/'.$modversion->mod->id.'#versions') }}" class="text-blue-600 dark:text-blue-400 hover:underline text-sm">Manage</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-5 py-8 text-center text-sm text-gray-500 dark:text-gray-400">No recently added mod versions.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Unused Mod Versions --}}
        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-800" x-data="{ open: true }">
            <button @click="open = !open" class="w-full flex items-center justify-between px-5 py-4 text-left">
                <h2 class="font-semibold text-gray-900 dark:text-white">
                    Unused Mod Versions
                    <span class="ml-2 inline-flex items-center justify-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400">{{ $unusedModversions->count() }}</span>
                </h2>
                <svg class="size-5 text-gray-400 transition-transform" :class="open && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>
            </button>
            <div x-show="open" x-collapse>
                <div class="overflow-x-auto border-t border-gray-200 dark:border-gray-800">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-800/50 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                <th class="px-5 py-3">Mod Name</th>
                                <th class="px-5 py-3">Version</th>
                                <th class="px-5 py-3">MD5</th>
                                <th class="px-5 py-3">Filesize</th>
                                <th class="px-5 py-3">Created</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                            @forelse ($unusedModversions as $modversion)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30">
                                    <td class="px-5 py-3">
                                        <a href="{{ url('/mod/view/'.$modversion->mod->id) }}" class="text-blue-600 dark:text-blue-400 hover:underline">
                                            {{ $modversion->mod->pretty_name ?: $modversion->mod->name }}
                                        </a>
                                    </td>
                                    <td class="px-5 py-3 font-mono text-sm">{{ $modversion->version }}</td>
                                    <td class="px-5 py-3 font-mono text-xs text-gray-500 dark:text-gray-400">{{ Str::limit($modversion->md5, 12) }}</td>
                                    <td class="px-5 py-3 text-gray-500 dark:text-gray-400">{{ $modversion->filesize ? $modversion->humanFilesize() : 'N/A' }}</td>
                                    <td class="px-5 py-3 text-gray-500 dark:text-gray-400">{{ $modversion->created_at->diffForHumans() }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-8 text-center text-sm text-gray-500 dark:text-gray-400">No unused mod versions found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Changelog --}}
        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-800" x-data="{ open: true }">
            <button @click="open = !open" class="w-full flex items-center justify-between px-5 py-4 text-left">
                <h2 class="font-semibold text-gray-900 dark:text-white">Changelog</h2>
                <svg class="size-5 text-gray-400 transition-transform" :class="open && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>
            </button>
            <div x-show="open" x-collapse>
                <div class="px-5 pb-5 border-t border-gray-200 dark:border-gray-800 pt-4">
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4 leading-relaxed">
                        Running Solder
                        @if (Cache::get('update'))
                            <code class="ml-1.5 px-2 py-0.5 rounded bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 text-xs font-semibold align-middle">v{{ SOLDER_VERSION }}</code>
                            <span class="text-xs text-red-600 dark:text-red-400">(update available)</span>
                        @else
                            <code class="ml-1.5 px-2 py-0.5 rounded bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 text-xs font-semibold align-middle">v{{ SOLDER_VERSION }}</code>
                        @endif
                    </p>
                    @if (array_key_exists('error', $changelog))
                        <div class="p-3 rounded-lg bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 text-sm text-yellow-700 dark:text-yellow-400/80">
                            {{ $changelog['error'] }}
                        </div>
                    @else
                        <div class="divide-y divide-gray-100 dark:divide-gray-800">
                            @foreach ($changelog as $change)
                                <a href="{{ $change['html_url'] }}" target="_blank" rel="noopener noreferrer"
                                   class="flex items-start gap-3 py-2.5 group hover:bg-gray-50 dark:hover:bg-gray-800/40 -mx-2 px-2 rounded transition-colors">
                                    <code class="shrink-0 mt-0.5 px-1.5 py-0.5 rounded bg-gray-100 dark:bg-gray-800 text-[11px] text-gray-500 dark:text-gray-400 group-hover:text-gray-700 dark:group-hover:text-gray-300 transition-colors">{{ substr($change['sha'], 0, 7) }}</code>
                                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ explode("\n", $change['commit']['message'], 2)[0] }}</span>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <p class="mt-6 text-sm text-gray-500 dark:text-gray-500">
        <a href="https://github.com/TechnicPack/TechnicSolder" target="_blank" rel="noopener noreferrer" class="hover:underline">Technic Solder</a> is open source, under the MIT license.
    </p>
@endsection
