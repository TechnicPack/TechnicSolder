<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Two-Factor Challenge &middot; Technic Solder</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    @fonts
    @include('partial.dark-mode-script')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 dark:bg-gray-950 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-sm" x-data="{ useRecovery: false }">
        <div class="text-center mb-8">
            <img src="{{ asset('img/wrenchIcon.svg') }}" alt="Solder" class="h-16 mx-auto mb-4">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Two-Factor Authentication</h1>
        </div>

        <div class="ui-card p-6">
            @error('code')
                <div class="ui-alert ui-alert-danger mb-4 p-3">
                    {{ $message }}
                </div>
            @enderror
            @error('recovery_code')
                <div class="ui-alert ui-alert-danger mb-4 p-3">
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
                        <label for="code" class="ui-label">Authentication Code</label>
                        <input type="text" name="code" id="code" inputmode="numeric" autocomplete="one-time-code" autofocus
                               class="ui-control"
                               placeholder="000000">
                    </div>
                    <div x-show="useRecovery" style="display: none">
                        <label for="recovery_code" class="ui-label">Recovery Code</label>
                        <input type="text" name="recovery_code" id="recovery_code"
                               class="ui-control">
                    </div>
                    <button type="submit"
                            class="ui-btn ui-btn-primary-solid w-full py-2.5">
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
