@extends('layouts.master')
@section('title')
    <title>Main Settings - Technic Solder</title>
@stop
@section('content')
    <h1 class="text-2xl font-bold">Configure Solder</h1>

    {{-- Main Settings --}}
    <div class="ui-card mt-6">
        <div class="ui-card-header">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Main Settings</h2>
        </div>
        <div class="px-5 py-4">
            @session('success')
                <div class="ui-alert ui-alert-success mb-4 p-4">
                    {{ $value }}
                </div>
            @endsession

            <div class="space-y-5 max-w-2xl">
                <div>
                    <label for="mirror_url" class="ui-label">Repository Mirror URL</label>
                    <input type="text"
                           name="mirror_url"
                           id="mirror_url"
                           value="{{ config('solder.mirror_url') }}"
                           disabled
                           class="ui-control-disabled">
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        This is the public facing URL for your repository. If your repository
                        location is already a URL, you can use the same value here. Include a trailing slash!
                    </p>
                </div>

                <div>
                    <label for="repo_location" class="ui-label">Repository Location</label>
                    <input type="text"
                           name="repo_location"
                           id="repo_location"
                           value="{{ config('solder.repo_location') }}"
                           disabled
                           class="ui-control-disabled">
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        This is the location of your mod repository. This can be a URL (remote
                        repo), or an absolute file location (local repo, much faster). When a remote repo is used,
                        Solder will have to download the entire file to calculate the MD5 hash.
                    </p>
                    <div class="ui-alert ui-alert-info mt-2 p-3">
                        The repository location is the prime suspect when MD5 hashing fails.
                        Most cases are caused by improper file permissions when using an absolute file location.
                    </div>
                </div>

                <div>
                    <label for="md5_connect_timeout" class="ui-label">Remote MD5 Connect Timeout</label>
                    <input type="text"
                           name="md5_connect_timeout"
                           id="md5_connect_timeout"
                           value="{{ config('solder.md5_connect_timeout') }}"
                           disabled
                           class="ui-control-disabled">
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        This is the amount of time (in seconds) Solder will wait before giving
                        up trying to connect to a URL to hash a mod.
                    </p>
                </div>

                <div>
                    <label for="md5_file_timeout" class="ui-label">Remote MD5 Total Timeout</label>
                    <input type="text"
                           name="md5_file_timeout"
                           id="md5_file_timeout"
                           value="{{ config('solder.md5_file_timeout') }}"
                           disabled
                           class="ui-control-disabled">
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        This is the amount of time (in seconds) Solder will attempt to remotely
                        hash a mod for before giving up.
                    </p>
                </div>
            </div>

            <p class="mt-4 text-sm text-gray-600 dark:text-gray-400">
                You can change these settings in the <strong class="text-gray-900 dark:text-gray-100">.env</strong> file.
            </p>
        </div>
    </div>

    {{-- Minecraft Versions Caching --}}
    <div class="ui-card mt-6"
         x-data="{
             loading: false,
             status: '{{ Cache::has('minecraftversions') ? 'cached' : 'not-cached' }}',
             message: '{{ Cache::has('minecraftversions') ? 'Minecraft versions are currently cached.' : 'Minecraft versions are not cached. This may cause unexpectedly long page loads the first time it loads them.' }}',
             async cacheVersions() {
                 this.loading = true;
                 try {
                     const data = await window.ajax('{{ URL::to('solder/cache-minecraft/') }}/');
                     if (data.success) {
                         this.status = 'cached';
                         this.message = 'Minecraft version caching complete.';
                     } else {
                         this.status = 'error';
                         this.message = 'Error caching Minecraft versions: ' + data.message;
                     }
                 } catch (e) {
                     this.status = 'error';
                     this.message = 'Error caching Minecraft versions. ' + e.message;
                 }
                 this.loading = false;
             }
         }">
        <div class="ui-card-header">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Minecraft Versions Caching</h2>
        </div>
        <div class="px-5 py-4">
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                Solder caches a list of Minecraft versions. The first attempt is from the Technic Platform. If
                that fails, it tries to get them from Mojang. The result is then cached for 3 hours. You can
                manually update the cache below.
            </p>

            <div x-show="status === 'cached'"
                 class="ui-alert ui-alert-success mb-4 p-4"
                 x-text="message"></div>

            <div x-show="status === 'not-cached'"
                 class="ui-alert ui-alert-warning mb-4 p-4"
                 x-text="message"></div>

            <div x-show="status === 'error'"
                 style="display: none"
                 class="ui-alert ui-alert-danger mb-4 p-4 dark:text-red-400/80"
                 x-text="message"></div>

            <div class="flex items-center gap-3">
                <button type="button"
                        @click="cacheVersions()"
                        :disabled="loading"
                        class="ui-btn ui-btn-secondary disabled:opacity-50 disabled:cursor-not-allowed">
                    <span x-show="!loading" x-text="status === 'cached' ? 'Update Cache' : 'Cache'"></span>
                    <span x-show="loading" style="display: none" class="flex items-center gap-2">
                        <svg class="size-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        Caching...
                    </span>
                </button>
            </div>
        </div>
    </div>
@endsection
