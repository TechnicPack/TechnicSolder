@extends('layouts.master')
@section('title')
    <title>New Build - {{ $modpack->name }} - Technic Solder</title>
@stop
@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Build Management</h1>
        <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Create a new build for {{ $modpack->name }}</p>
    </div>

    <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-800">
        <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-800">
            <span class="font-semibold text-gray-900 dark:text-white">Create New Build ({{ $modpack->name }})</span>
        </div>
        <div class="p-5">
            @include('partial.form-errors')
            <form action="{{ url()->current() }}" method="post" accept-charset="UTF-8"
                  x-data="{ memoryEnabled: false }">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    {{-- Left column: Build info --}}
                    <div class="space-y-5">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white">Create Build</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400">All new builds by default will not be available in the API. They need to be published before they will show up.</p>
                        <hr class="border-gray-200 dark:border-gray-700">
                        <div>
                            <label for="version" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Build Number</label>
                            <input type="text"
                                   name="version"
                                   id="version"
                                   autofocus
                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors">
                        </div>
                        <div>
                            <label for="minecraft" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Minecraft Version</label>
                            <select name="minecraft"
                                    id="minecraft"
                                    class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors">
                                @foreach ($minecraft as $version)
                                    <option value="{{ $version['version'] }}">{{ $version['version'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="clone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Clone Build</label>
                            <select name="clone"
                                    id="clone"
                                    class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors">
                                <option value="">Do not clone</option>
                                @foreach ($modpack->builds as $build)
                                    <option value="{{ $build->id }}">{{ $build->version }}</option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">This will clone all the mods and mod versions of another build in this pack.</p>
                        </div>
                    </div>

                    {{-- Right column: Requirements --}}
                    <div class="space-y-5">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white">Build Requirements</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400">These are requirements that are passed onto the launcher to prevent players from playing your pack without the required minimum settings.</p>
                        <hr class="border-gray-200 dark:border-gray-700">
                        <div>
                            <label for="java-version" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Minimum Java Version</label>
                            <select name="java-version"
                                    id="java-version"
                                    class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors">
                                @foreach(\App\JavaVersionsEnum::cases() as $java)
                                    <option value="{{ $java->value }}"
                                            @selected(old('java-version') === $java->value)
                                    >Java {{ $java->value }}</option>
                                @endforeach
                                <option value="" @selected(!old('java-version'))>No Requirement</option>
                            </select>
                        </div>
                        <div>
                            <label for="memory" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Minimum Memory <span class="text-gray-400 font-normal">(in MB)</span></label>
                            <div class="flex items-center gap-3">
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="checkbox"
                                           name="memory-enabled"
                                           x-model="memoryEnabled"
                                           class="rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500 dark:bg-gray-800">
                                </label>
                                <div class="flex-1 relative">
                                    <input type="number"
                                           name="memory"
                                           id="memory"
                                           :disabled="!memoryEnabled"
                                           class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                           placeholder="e.g. 2048">
                                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-sm text-gray-400 pointer-events-none">MB</span>
                                </div>
                            </div>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Check the checkbox to enable the memory requirement.</p>
                        </div>
                    </div>
                </div>

                <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700 flex items-center gap-3">
                    <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white dark:bg-blue-500/15 dark:text-blue-400 dark:hover:bg-blue-500/25 font-medium py-2 px-4 rounded-lg text-sm transition-colors">
                        Add Build
                    </button>
                    <a href="{{ url('/modpack/view/'.$modpack->id) }}"
                       class="bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 font-medium py-2 px-4 rounded-lg text-sm transition-colors">
                        Go Back
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
