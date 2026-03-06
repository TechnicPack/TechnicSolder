@extends('layouts.master')
@section('title')
    <title>Update Checker - Technic Solder</title>
@stop
@section('content')
    <h1 class="text-2xl font-bold">Update Manager</h1>

    <div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Left column --}}
        <div class="space-y-6">
            {{-- Solder Versioning --}}
            <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-800">
                <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-800">
                    <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Solder Versioning</h2>
                </div>
                <div class="px-5 py-4 space-y-2">
                    <div class="flex items-center gap-2 text-sm">
                        <span class="font-medium text-gray-700 dark:text-gray-300">Current Version:</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300">{{ $currentVersion }}</span>
                    </div>
                    <div class="flex items-center gap-2 text-sm">
                        <span class="font-medium text-gray-700 dark:text-gray-300">Latest Version:</span>
                        @if (is_array($latestData['version']) && array_key_exists('error', $latestData['version']))
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300">{{ $latestData['version']['error'] }}</span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300">{{ $latestData['version'] }}</span>
                        @endif
                    </div>
                    <div class="flex items-center gap-2 text-sm">
                        <span class="font-medium text-gray-700 dark:text-gray-300">Latest Commit:</span>
                        @if (array_key_exists('error', $changelog))
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300">{{ $latestData['commit']['error'] }}</span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 font-mono">{{ $latestData['commit']['sha'] }}</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Update Check --}}
            <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-800"
                 x-data="{
                     loading: false,
                     status: '{{ Cache::get('update') ? 'outdated' : 'up-to-date' }}',
                     async checkUpdate() {
                         this.loading = true;
                         try {
                             const data = await window.ajax('{{ URL::to('solder/update-check/') }}/');
                             if (data.success) {
                                 this.status = data.update ? 'outdated' : 'up-to-date';
                             } else {
                                 this.status = 'error';
                                 this.errorMessage = data.reason;
                             }
                         } catch (e) {
                             this.status = 'error';
                             this.errorMessage = e.message;
                         }
                         this.loading = false;
                     },
                     errorMessage: ''
                 }">
                <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-800">
                    <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Update Check</h2>
                </div>
                <div class="px-5 py-4">
                    <div x-show="status === 'outdated'"
                         class="mb-4 p-4 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-sm text-red-700 dark:text-red-300">
                        Solder is out of date. Please refer to the wiki on how to update.
                    </div>

                    <div x-show="status === 'up-to-date'"
                         class="mb-4 p-4 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-sm text-green-700 dark:text-green-300">
                        Solder is up to date.
                    </div>

                    <div x-show="status === 'error'"
                         style="display: none"
                         class="mb-4 p-4 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-sm text-red-700 dark:text-red-300">
                        Error checking for update: <span x-text="errorMessage"></span>
                    </div>

                    <div class="flex items-center gap-3">
                        <a href="https://docs.solder.io/docs/updating-solder" target="_blank"
                           class="bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 font-medium py-2 px-4 rounded-lg text-sm transition-colors">
                            Updating Solder
                        </a>
                        <button type="button"
                                @click="checkUpdate()"
                                :disabled="loading"
                                class="bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 font-medium py-2 px-4 rounded-lg text-sm transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                            <span x-show="!loading">Check for update</span>
                            <span x-show="loading" style="display: none" class="flex items-center gap-2">
                                <svg class="size-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                Checking...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right column: Activity Panel --}}
        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-800">
            <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-800">
                <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                    <svg class="size-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                    Activity Panel
                </h2>
            </div>
            <div class="px-5 py-4">
                @if (array_key_exists('error', $changelog))
                    <div class="mb-4 p-4 rounded-lg bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 text-sm text-yellow-700 dark:text-yellow-400/80">
                        {{ $changelog['error'] }}
                    </div>
                @else
                    <div class="divide-y divide-gray-200 dark:divide-gray-800">
                        @foreach ($changelog as $change)
                            <a href="{{ $change['html_url'] }}" target="_blank"
                               class="flex items-start gap-3 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800/50 -mx-2 px-2 rounded transition-colors">
                                <img src="{{ $change['author']['avatar_url'] ?? $change['committer']['avatar_url'] }}"
                                     alt="{{ $change['author']['login'] ?? $change['commit']['author']['name'] ?? $change['committer']['login'] }}"
                                     class="size-6 rounded-full shrink-0 mt-0.5">
                                <div class="flex-1 min-w-0">
                                    <p class="truncate">{{ $change['commit']['message'] }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                        {{ date_format(date_create($change['commit']['author']['date']), 'M, d Y - g:i a') }}
                                    </p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif

                <div class="mt-4 text-right">
                    <a href="https://github.com/TechnicPack/TechnicSolder/commits/master"
                       target="_blank"
                       class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                        View All Activity &rarr;
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
