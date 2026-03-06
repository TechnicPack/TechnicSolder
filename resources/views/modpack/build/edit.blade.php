@extends('layouts.master')
@section('title')
    <title>{{ $build->version }} &ndash; {{ $build->modpack->name }} &ndash; Technic Solder</title>
@stop
@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Build Management</h1>
        <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Edit build {{ $build->version }} for {{ $build->modpack->name }}</p>
    </div>

    <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-800">
        <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-800 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <span class="font-semibold text-gray-900 dark:text-white">{{ $build->modpack->name }} &mdash; build {{ $build->version }}</span>
            <div class="flex items-center gap-2">
                <a href="{{ url('/modpack/build/' . $build->id) }}"
                   class="bg-blue-600 hover:bg-blue-700 text-white dark:bg-blue-500/15 dark:text-blue-400 dark:hover:bg-blue-500/25 font-medium py-1.5 px-3 text-xs rounded-lg transition-colors">
                    Add mods
                </a>
                <a href="{{ url('modpack/view/' . $build->modpack->id) }}"
                   class="bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 font-medium py-1.5 px-3 text-xs rounded-lg transition-colors">
                    Back to modpack
                </a>
            </div>
        </div>
        <div class="p-5">
            @if ($build->is_published)
                <div class="mb-4 p-4 rounded-lg bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 text-sm text-yellow-700 dark:text-yellow-400/80">
                    If changes are made, users will need to re-install the modpack if they have already installed this build.
                </div>
            @endif

            @include('partial.form-errors')

            <form action="{{ url('/modpack/build/'.$build->id.'/edit') }}" method="post" accept-charset="UTF-8"
                  x-data="{
                      memoryEnabled: {{ old('memory-enabled', $build->min_memory) ? 'true' : 'false' }},
                      memoryValue: '{{ old('memory', $build->min_memory) ?: '' }}',
                      savedMemory: '{{ old('memory', $build->min_memory) ?: '' }}'
                  }">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    {{-- Left column: Build info --}}
                    <div class="space-y-5">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white">Edit Build</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Here you can modify the properties of existing builds.</p>
                        <hr class="border-gray-200 dark:border-gray-700">
                        <div>
                            <label for="version" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Build name</label>
                            <input type="text"
                                   name="version"
                                   id="version"
                                   value="{{ old('version', $build->version) }}"
                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors">
                        </div>
                        <div>
                            <label for="minecraft" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Minecraft version</label>
                            <select name="minecraft"
                                    id="minecraft"
                                    class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors">
                                @foreach ($minecraft as $version)
                                    <option value="{{ $version['version'] }}"
                                            @selected($build->minecraft == $version['version'])
                                    >{{ $version['version'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Right column: Requirements --}}
                    <div class="space-y-5">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white">Build Requirements</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400">These requirements are passed to the launcher and prevent players without the required minimum settings from playing your modpack.</p>
                        <hr class="border-gray-200 dark:border-gray-700">
                        <div>
                            <label for="java-version" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Required Java version (at least)</label>
                            <select name="java-version"
                                    id="java-version"
                                    class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors">
                                @foreach(\App\JavaVersionsEnum::cases() as $java)
                                    <option value="{{ $java->value }}"
                                            @selected(old('java-version', $build->min_java) === $java->value)
                                    >Java {{ $java->value }}</option>
                                @endforeach
                                <option value="" @selected(!old('java-version', $build->min_java))>No Requirement</option>
                            </select>
                        </div>
                        <div>
                            <label for="memory" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Required RAM/memory <span class="text-gray-400 font-normal">(in MB)</span></label>
                            <div class="flex items-center gap-3">
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="checkbox"
                                           name="memory-enabled"
                                           x-model="memoryEnabled"
                                           x-effect="if (!memoryEnabled) { savedMemory = memoryValue; memoryValue = ''; } else if (!memoryValue) { memoryValue = savedMemory; }"
                                           class="rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500 dark:bg-gray-800">
                                </label>
                                <div class="flex-1 relative">
                                    <input type="number"
                                           name="memory"
                                           id="memory"
                                           x-model="memoryValue"
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
                        Save changes
                    </button>
                    <a href="{{ url('/modpack/build/'.$build->id) }}"
                       class="bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 font-medium py-2 px-4 rounded-lg text-sm transition-colors">
                        Go back
                    </a>
                    <a href="{{ url('/modpack/build/'.$build->id.'/delete') }}"
                       class="ml-auto bg-red-600 hover:bg-red-700 text-white dark:bg-red-500/15 dark:text-red-400 dark:hover:bg-red-500/25 font-medium py-2 px-4 rounded-lg text-sm transition-colors">
                        Delete build
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
