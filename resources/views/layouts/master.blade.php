<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    @section('title')
        <title>Technic Solder</title>
    @show
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet">
    @include('partial.dark-mode-script')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="bg-gray-50 text-gray-900 dark:bg-gray-950 dark:text-gray-100 min-h-screen"
      x-data="{
          sidebarOpen: false,
          sections: { modpacks: false, mods: false, settings: false },
          init() {
              const p = window.location.pathname;
              if (p.startsWith('/modpack')) this.sections.modpacks = true;
              if (p.startsWith('/mod')) this.sections.mods = true;
              if (p.startsWith('/solder') || p.startsWith('/user') || p.startsWith('/client') || p.startsWith('/key')) this.sections.settings = true;
          }
      }">

    {{-- Mobile sidebar overlay --}}
    <div x-show="sidebarOpen"
         x-transition:enter="transition-opacity duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="sidebarOpen = false"
         class="fixed inset-0 z-20 bg-black/50 lg:hidden"
         style="display: none"></div>

    {{-- Sidebar --}}
    <aside class="fixed inset-y-0 left-0 z-30 w-64 bg-slate-800 dark:bg-slate-800 text-white flex flex-col transform transition-transform duration-200 lg:translate-x-0"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">

        {{-- Logo --}}
        <div class="flex items-center gap-3 px-5 h-16 border-b border-slate-700 shrink-0">
            <img src="{{ asset('img/title.png') }}" alt="Solder" class="h-9">
            <span class="text-xs text-slate-400">v{{ SOLDER_VERSION }}</span>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 overflow-y-auto sidebar-scroll py-3">
            {{-- Dashboard --}}
            <a href="{{ url('dashboard') }}"
               @class([
                   'flex items-center gap-3 px-5 py-2.5 text-sm transition-colors',
                   'bg-slate-700/50 text-white' => Request::is('dashboard'),
                   'text-slate-300 hover:bg-slate-700/30 hover:text-white' => !Request::is('dashboard'),
               ])>
                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/></svg>
                Dashboard
            </a>

            {{-- Modpacks section --}}
            <div class="mt-1">
                <button @click="sections.modpacks = !sections.modpacks"
                        @class([
                            'flex items-center justify-between w-full px-5 py-2.5 text-sm transition-colors',
                            'text-white bg-slate-700/50' => Request::is('modpack*'),
                            'text-slate-300 hover:bg-slate-700/30 hover:text-white' => !Request::is('modpack*'),
                        ])>
                    <span class="flex items-center gap-3">
                        <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 0 1 4.5 9.75h15A2.25 2.25 0 0 1 21.75 12v.75m-8.69-6.44-2.12-2.12a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9a2.25 2.25 0 0 0-2.25-2.25h-5.379a1.5 1.5 0 0 1-1.06-.44Z"/></svg>
                        Modpacks
                    </span>
                    <svg class="size-4 transition-transform" :class="sections.modpacks && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>
                </button>
                <div x-show="sections.modpacks" x-collapse
                     x-data="{
                         modpackSearch: '',
                         modpacks: @js($allModpacks->map(fn($mp) => [
                             'id' => $mp->id,
                             'name' => $mp->name,
                             'slug' => $mp->slug,
                             'icon_url' => $mp->icon_url ?? asset('/resources/default/icon.png'),
                             'hidden' => $mp->hidden,
                         ])->values()),
                         get filteredModpacks() {
                             if (!this.modpackSearch) return this.modpacks;
                             const s = this.modpackSearch.toLowerCase();
                             return this.modpacks.filter(mp => mp.name.toLowerCase().includes(s));
                         }
                     }">
                    <a href="{{ url('modpack/list') }}" class="block pl-13 pr-5 py-1.5 text-sm text-slate-300 hover:text-white transition-colors">Modpack List</a>
                    <a href="{{ url('modpack/create') }}" class="block pl-13 pr-5 py-1.5 text-sm text-slate-300 hover:text-white transition-colors">Add Modpack</a>
                    <div class="px-3 py-1.5">
                        <input type="text"
                               x-init="$watch('sections.modpacks', v => { if (v) setTimeout(() => $el.focus(), 200) })"
                               x-model="modpackSearch"
                               @keydown.enter="if (filteredModpacks.length === 1) window.location.href = '/modpack/view/' + filteredModpacks[0].id"
                               placeholder="Search modpacks..."
                               class="w-full px-2.5 py-1.5 text-xs rounded-md bg-slate-700/50 border border-slate-600 text-slate-200 placeholder-slate-300 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                        <p x-show="modpackSearch && filteredModpacks.length === 1" x-cloak
                           class="mt-1 text-[10px] text-slate-400">Press <kbd class="px-1 py-0.5 rounded bg-slate-700 text-slate-300">Enter</kbd> to open</p>
                    </div>
                    <template x-for="mp in filteredModpacks" :key="mp.id">
                        <a :href="'/modpack/view/' + mp.id"
                           class="flex items-center gap-2 pl-13 pr-5 py-1.5 text-sm text-slate-300 hover:text-white transition-colors truncate">
                            <img :src="mp.icon_url"
                                 class="size-4 rounded shrink-0" alt="">
                            <span class="truncate" x-text="mp.name"></span>
                            <template x-if="mp.hidden">
                                <svg class="shrink-0 text-slate-400" style="width:14px;height:14px" title="Hidden" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12c1.292 4.338 5.31 7.5 10.066 7.5.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                            </template>
                        </a>
                    </template>
                    <p x-show="modpackSearch && filteredModpacks.length === 0"
                       class="pl-13 pr-5 py-1.5 text-xs text-slate-500 italic">No modpacks found.</p>
                </div>
            </div>

            {{-- Mod Library section --}}
            <div class="mt-1">
                <button @click="sections.mods = !sections.mods"
                        @class([
                            'flex items-center justify-between w-full px-5 py-2.5 text-sm transition-colors',
                            'text-white bg-slate-700/50' => Request::is('mod*') && !Request::is('modpack*'),
                            'text-slate-300 hover:bg-slate-700/30 hover:text-white' => !(Request::is('mod*') && !Request::is('modpack*')),
                        ])>
                    <span class="flex items-center gap-3">
                        <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"/></svg>
                        Mod Library
                    </span>
                    <svg class="size-4 transition-transform" :class="sections.mods && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>
                </button>
                <div x-show="sections.mods" x-collapse>
                    <a href="{{ url('mod/list') }}" class="block pl-13 pr-5 py-1.5 text-sm text-slate-300 hover:text-white transition-colors">Mod List</a>
                    <a href="{{ url('mod/create') }}" class="block pl-13 pr-5 py-1.5 text-sm text-slate-300 hover:text-white transition-colors">Add a Mod</a>
                </div>
            </div>

            {{-- Settings section --}}
            <div class="mt-1">
                <button @click="sections.settings = !sections.settings"
                        @class([
                            'flex items-center justify-between w-full px-5 py-2.5 text-sm transition-colors',
                            'text-white bg-slate-700/50' => Request::is('solder*') || Request::is('user*') || Request::is('client*') || Request::is('key*'),
                            'text-slate-300 hover:bg-slate-700/30 hover:text-white' => !(Request::is('solder*') || Request::is('user*') || Request::is('client*') || Request::is('key*')),
                        ])>
                    <span class="flex items-center gap-3">
                        <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                        Settings
                    </span>
                    <svg class="size-4 transition-transform" :class="sections.settings && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>
                </button>
                <div x-show="sections.settings" x-collapse>
                    <a href="{{ url('solder/configure') }}" class="block pl-13 pr-5 py-1.5 text-sm text-slate-300 hover:text-white transition-colors">Configuration</a>
                    <a href="{{ url('solder/update') }}" class="block pl-13 pr-5 py-1.5 text-sm text-slate-300 hover:text-white transition-colors">Update Checker</a>
                    <a href="{{ url('user/list') }}" class="block pl-13 pr-5 py-1.5 text-sm text-slate-300 hover:text-white transition-colors">Users</a>
                    <a href="{{ url('client/list') }}" class="block pl-13 pr-5 py-1.5 text-sm text-slate-300 hover:text-white transition-colors">Clients</a>
                    <a href="{{ url('key/list') }}" class="block pl-13 pr-5 py-1.5 text-sm text-slate-300 hover:text-white transition-colors">Platform Keys</a>
                </div>
            </div>
        </nav>

        {{-- Sidebar footer --}}
        <div class="border-t border-slate-700 px-5 py-3 text-xs text-slate-500 shrink-0">
            Technic Solder v{{ SOLDER_VERSION }}
        </div>
    </aside>

    {{-- Main content area --}}
    <div class="lg:pl-64 min-h-screen flex flex-col min-w-0">
        {{-- Top bar --}}
        <header class="sticky top-0 z-10 bg-white/80 dark:bg-gray-900/80 backdrop-blur-sm border-b border-gray-200 dark:border-gray-800 h-16 flex items-center justify-between px-4 lg:px-6 shrink-0">
            <div class="flex items-center gap-3">
                {{-- Mobile menu button --}}
                <button @click="sidebarOpen = true" class="lg:hidden p-2 -ml-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
                </button>

                @if (Cache::get('update'))
                    <a href="{{ url('solder/update') }}" class="text-sm text-orange-600 dark:text-orange-400 font-medium flex items-center gap-1.5">
                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>
                        Update Available
                    </a>
                @endif
            </div>

            <div class="flex items-center gap-2">
                {{-- Dark mode toggle --}}
                <button @click="$store.darkMode.toggle()"
                        class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                    <svg x-show="!$store.darkMode.on" class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z"/></svg>
                    <svg x-show="$store.darkMode.on" class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="display: none"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z"/></svg>
                </button>

                {{-- User dropdown --}}
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" @click.outside="open = false"
                            class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-colors">
                        <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                        {{ Auth::user()->username }}
                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>
                    </button>
                    <div x-show="open" x-transition
                         class="absolute right-0 mt-1 w-48 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg py-1"
                         style="display: none">
                        <a href="{{ url('user/edit/'.Auth::user()->id) }}"
                           class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/></svg>
                            Profile
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                    class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 w-full text-left">
                                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15m-3 0-3-3m0 0 3-3m-3 3H15"/></svg>
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        {{-- Page content --}}
        <main class="flex-1 p-4 lg:p-6">
            @yield('content')
        </main>
    </div>

    {{-- Toast notifications --}}
    <div class="fixed bottom-4 right-4 z-50 flex flex-col items-end gap-2" x-data>
        <template x-for="toast in $store.toasts.items" :key="toast.id">
            <div x-show="true"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0 translate-y-2"
                 :class="{
                     'bg-green-50 text-green-800 border-green-200 dark:bg-green-950 dark:text-green-300 dark:border-green-800': toast.type === 'success',
                     'bg-red-50 text-red-800 border-red-200 dark:bg-red-950 dark:text-red-300 dark:border-red-800': toast.type === 'error',
                     'bg-yellow-50 text-yellow-800 border-yellow-200 dark:bg-yellow-950 dark:text-yellow-300 dark:border-yellow-800': toast.type === 'warning',
                     'bg-blue-50 text-blue-800 border-blue-200 dark:bg-blue-950 dark:text-blue-300 dark:border-blue-800': toast.type === 'info',
                 }"
                 class="px-4 py-3 rounded-lg border shadow-lg max-w-sm cursor-pointer text-sm"
                 @click="$store.toasts.remove(toast.id)"
                 x-text="toast.message">
            </div>
        </template>
    </div>

    @stack('scripts')
</body>
</html>
