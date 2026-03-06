@extends('layouts.master')
@section('title')
    <title>Mod Management - Technic Solder</title>
@stop
@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold">Mod Library</h1>
    </div>

    <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-800">
        <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-800">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Delete Request for {{ $mod->name }}</h2>
        </div>
        <div class="p-5">
            <div class="mb-4 p-4 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800">
                <p class="text-sm text-red-700 dark:text-red-300 mb-2">
                    Deleting a mod can have serious repercussions. All associated modpacks and builds using this mod will
                    have the mod forcefully stripped from their build history. This includes every version of the mod in
                    every build it existed in. This tool should mainly be used to delete a mod you created by mistake or are
                    entirely sure is no longer needed.
                </p>
                <p class="text-sm text-red-700 dark:text-red-300 font-semibold">
                    Please carefully check the versions list before removing the mod!
                </p>
                <p class="text-sm text-red-700 dark:text-red-300 mt-2 font-semibold">
                    Deleting a mod is irreversible!
                </p>
            </div>

            <div x-data="{ expanded: false }" class="mb-6">
                <button @click="expanded = !expanded"
                        type="button"
                        class="flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-gray-100 transition-colors">
                    <svg class="size-4 transition-transform" :class="expanded && 'rotate-90'" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/>
                    </svg>
                    Versions ({{ $mod->versions->count() }})
                </button>
                <div x-show="expanded" x-collapse class="mt-3 ml-6">
                    @if ($mod->versions->isEmpty())
                        <p class="text-sm text-gray-500 dark:text-gray-400">No versions found.</p>
                    @else
                        <ul class="space-y-2">
                            @foreach ($mod->versions as $ver)
                                <li>
                                    <span class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $ver->version }}</span>
                                    @if ($ver->builds->isNotEmpty())
                                        <ul class="ml-4 mt-1 space-y-1">
                                            @foreach ($ver->builds as $build)
                                                <li class="text-sm text-gray-600 dark:text-gray-400">
                                                    {{ $build->modpack->name }} - {{ $build->version }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            <hr class="border-gray-200 dark:border-gray-800 mb-6">

            <form action="{{ url()->current() }}" method="post" accept-charset="UTF-8">
                @csrf
                <div class="flex items-center gap-3">
                    <button type="submit"
                            class="bg-red-600 hover:bg-red-700 text-white dark:bg-red-500/15 dark:text-red-400 dark:hover:bg-red-500/25 font-medium py-2 px-4 rounded-lg text-sm transition-colors">
                        Delete Mod
                    </button>
                    <a href="{{ url('/mod/list') }}"
                       class="bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 font-medium py-2 px-4 rounded-lg text-sm transition-colors">
                        Go Back
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
