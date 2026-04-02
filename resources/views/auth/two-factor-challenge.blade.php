<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Two-Factor Challenge &middot; Technic Solder</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet">
    @include('partial.dark-mode-script')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 dark:bg-gray-950 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-sm" x-data="{ useRecovery: false }">
        <div class="text-center mb-8">
            <img src="{{ asset('img/wrenchIcon.svg') }}" alt="Solder" class="h-16 mx-auto mb-4">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Two-Factor Authentication</h1>
        </div>

        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-800 p-6">
            @error('code')
                <div class="mb-4 p-3 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-sm text-red-700 dark:text-red-300">
                    {{ $message }}
                </div>
            @enderror
            @error('recovery_code')
                <div class="mb-4 p-3 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-sm text-red-700 dark:text-red-300">
                    {{ $message }}
                </div>
            @enderror

            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4" x-show="!useRecovery">
                Enter the 6-digit code from your authenticator app.
            </p>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4" x-show="useRecovery" style="display: none">
                Enter one of your recovery codes.
            </p>

            <form method="POST" action="{{ url('/two-factor-challenge') }}">
                @csrf
                <div class="space-y-4">
                    <div x-show="!useRecovery">
                        <label for="code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Authentication Code</label>
                        <input type="text" name="code" id="code" inputmode="numeric" autocomplete="one-time-code" autofocus
                               class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors"
                               placeholder="000000">
                    </div>
                    <div x-show="useRecovery" style="display: none">
                        <label for="recovery_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Recovery Code</label>
                        <input type="text" name="recovery_code" id="recovery_code"
                               class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors">
                    </div>
                    <button type="submit"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 px-4 rounded-lg text-sm transition-colors">
                        Verify
                    </button>
                    <button type="button" @click="useRecovery = !useRecovery"
                            class="w-full text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 transition-colors">
                        <span x-show="!useRecovery">Use a recovery code</span>
                        <span x-show="useRecovery" style="display: none">Use authenticator code</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
