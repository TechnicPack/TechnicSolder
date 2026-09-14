@extends('layouts.master')
@section('title')
    <title>{{ $build->version }} &ndash; {{ $build->modpack->name }} &ndash; Technic Solder</title>
@stop
@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Build Management</h1>
        <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Edit build {{ $build->version }} for {{ $build->modpack->name }}</p>
    </div>

    <div class="ui-card">
        <div class="ui-card-header flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <span class="font-semibold text-gray-900 dark:text-white">{{ $build->modpack->name }} &mdash; build {{ $build->version }}</span>
            <div class="flex items-center gap-2">
                <a href="{{ url('/modpack/build/' . $build->id) }}"
                   class="ui-btn ui-btn-sm ui-btn-primary">
                    Add mods
                </a>
                <a href="{{ url('modpack/view/' . $build->modpack->id) }}"
                   class="ui-btn ui-btn-sm ui-btn-secondary">
                    Back to modpack
                </a>
            </div>
        </div>
        <div class="p-5">
            @if ($build->is_published)
                <div class="ui-alert ui-alert-warning mb-4 p-4">
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
                            <label for="version" class="ui-label">Build name</label>
                            <input type="text"
                                   name="version"
                                   id="version"
                                   value="{{ old('version', $build->version) }}"
                                   class="ui-control">
                        </div>
                        <div>
                            <label for="minecraft" class="ui-label">Minecraft version</label>
                            <select name="minecraft"
                                    id="minecraft"
                                    class="ui-control">
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
                            <label for="java-version" class="ui-label">Required Java version (at least)</label>
                            <select name="java-version"
                                    id="java-version"
                                    class="ui-control">
                                @foreach(\App\JavaVersionsEnum::cases() as $java)
                                    <option value="{{ $java->value }}"
                                            @selected(old('java-version', $build->min_java) === $java->value)
                                    >Java {{ $java->value }}</option>
                                @endforeach
                                <option value="" @selected(!old('java-version', $build->min_java))>No Requirement</option>
                            </select>
                        </div>
                        @if (config('solder.advanced_mode'))
                        <div>
                            <label for="java-runtime" class="ui-label">Mojang Java Runtime Override</label>
                            <select name="java-runtime"
                                    id="java-runtime"
                                    aria-describedby="java-runtime-help java-runtime-support"
                                    class="ui-control">
                                <option value="" @selected(!old('java-runtime', $build->java_runtime))>Default (no override)</option>
                                @foreach(\App\JavaRuntimesEnum::cases() as $runtime)
                                    <option value="{{ $runtime->value }}"
                                            @selected(old('java-runtime', $build->java_runtime) === $runtime->value)
                                    >{{ $runtime->label() }}</option>
                                @endforeach
                            </select>
                            <p id="java-runtime-help" class="mt-1 text-sm text-gray-500 dark:text-gray-400">Selects the Mojang Java runtime for this build instead of the launcher's automatic selection. Applies when “Use Mojang Java runtimes” is enabled in the launcher. Leave as Default to keep automatic selection.</p>
                            <p id="java-runtime-support" class="mt-1 text-sm text-gray-500 dark:text-gray-400">Requires launcher support.</p>
                        </div>
                        @endif
                        <div>
                            <label for="memory" class="ui-label">Required RAM/memory <span class="text-gray-400 font-normal">(in MB)</span></label>
                            <div class="flex items-center gap-3">
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="checkbox"
                                           name="memory-enabled"
                                           x-model="memoryEnabled"
                                           x-effect="if (!memoryEnabled) { savedMemory = memoryValue; memoryValue = ''; } else if (!memoryValue) { memoryValue = savedMemory; }"
                                           class="ui-checkbox dark:bg-gray-800">
                                </label>
                                <div class="flex-1 relative">
                                    <input type="number"
                                           name="memory"
                                           id="memory"
                                           x-model="memoryValue"
                                           :disabled="!memoryEnabled"
                                           class="ui-control disabled:opacity-50 disabled:cursor-not-allowed"
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
                            class="ui-btn ui-btn-primary">
                        Save changes
                    </button>
                    <a href="{{ url('/modpack/build/'.$build->id) }}"
                       class="ui-btn ui-btn-secondary">
                        Go back
                    </a>
                    <a href="{{ url('/modpack/build/'.$build->id.'/delete') }}"
                       class="ui-btn ui-btn-danger ml-auto">
                        Delete build
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
