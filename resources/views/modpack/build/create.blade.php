@extends('layouts.master')
@section('title')
    <title>New Build - {{ $modpack->name }} - Technic Solder</title>
@stop
@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Build Management</h1>
        <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Create a new build for {{ $modpack->name }}</p>
    </div>

    <div class="ui-card">
        <div class="ui-card-header">
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
                            <label for="version" class="ui-label">Build Number</label>
                            <input type="text"
                                   name="version"
                                   id="version"
                                   autofocus
                                   class="ui-control">
                        </div>
                        <div>
                            <label for="minecraft" class="ui-label">Minecraft Version</label>
                            <select name="minecraft"
                                    id="minecraft"
                                    class="ui-control">
                                @foreach ($minecraft as $version)
                                    <option value="{{ $version['version'] }}">{{ $version['version'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="clone" class="ui-label">Clone Build</label>
                            <select name="clone"
                                    id="clone"
                                    class="ui-control">
                                <option value="">None</option>
                                @foreach ($cloneableModpacks as $mp)
                                    @if ($mp->builds->isNotEmpty())
                                        <optgroup label="{{ $mp->name }}{{ $mp->id === $modpack->id ? ' (current)' : '' }}">
                                            @foreach ($mp->builds as $b)
                                                <option value="{{ $b->id }}">{{ $b->version }}</option>
                                            @endforeach
                                        </optgroup>
                                    @endif
                                @endforeach
                            </select>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">This will clone all the mods and mod versions from an existing build.</p>
                        </div>
                    </div>

                    {{-- Right column: Requirements --}}
                    <div class="space-y-5">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white">Build Requirements</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400">These are requirements that are passed onto the launcher to prevent players from playing your pack without the required minimum settings.</p>
                        <hr class="border-gray-200 dark:border-gray-700">
                        <div>
                            <label for="java-version" class="ui-label">Minimum Java Version</label>
                            <select name="java-version"
                                    id="java-version"
                                    class="ui-control">
                                @foreach(\App\JavaVersionsEnum::cases() as $java)
                                    <option value="{{ $java->value }}"
                                            @selected(old('java-version') === $java->value)
                                    >Java {{ $java->value }}</option>
                                @endforeach
                                <option value="" @selected(!old('java-version'))>No Requirement</option>
                            </select>
                        </div>
                        @if (config('solder.advanced_mode'))
                        <div>
                            <label for="java-runtime" class="ui-label">Mojang Java Runtime Override</label>
                            <select name="java-runtime"
                                    id="java-runtime"
                                    aria-describedby="java-runtime-help java-runtime-support"
                                    class="ui-control">
                                <option value="" @selected(!old('java-runtime'))>Default (no override)</option>
                                @foreach(\App\JavaRuntimesEnum::cases() as $runtime)
                                    <option value="{{ $runtime->value }}"
                                            @selected(old('java-runtime') === $runtime->value)
                                    >{{ $runtime->label() }}</option>
                                @endforeach
                            </select>
                            <p id="java-runtime-help" class="mt-1 text-sm text-gray-500 dark:text-gray-400">Selects the Mojang Java runtime for this build instead of the launcher's automatic selection. Applies when “Use Mojang Java runtimes” is enabled in the launcher. Leave as Default to keep automatic selection.</p>
                            <p id="java-runtime-support" class="mt-1 text-sm text-gray-500 dark:text-gray-400">Requires launcher support.</p>
                        </div>
                        @endif
                        <div>
                            <label for="memory" class="ui-label">Minimum Memory <span class="text-gray-400 font-normal">(in MB)</span></label>
                            <div class="flex items-center gap-3">
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="checkbox"
                                           name="memory-enabled"
                                           x-model="memoryEnabled"
                                           class="ui-checkbox dark:bg-gray-800">
                                </label>
                                <div class="flex-1 relative">
                                    <input type="number"
                                           name="memory"
                                           id="memory"
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
                        Add Build
                    </button>
                    <a href="{{ url('/modpack/view/'.$modpack->id) }}"
                       class="ui-btn ui-btn-secondary">
                        Go Back
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
