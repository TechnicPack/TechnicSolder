<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Confirm Password &middot; Technic Solder</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    @fonts
    @include('partial.dark-mode-script')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 dark:bg-gray-950 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-sm">
        <div class="text-center mb-8">
            <img src="{{ asset('img/wrenchIcon.svg') }}" alt="Solder" class="h-16 mx-auto mb-4">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Confirm Password</h1>
        </div>

        <div class="ui-card p-6">
            @error('password')
                <div class="ui-alert ui-alert-danger mb-4 p-3">
                    {{ $message }}
                </div>
            @enderror

            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                Please confirm your password before continuing.
            </p>

            <form method="POST" action="{{ url('/user/confirm-password') }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label for="password" class="ui-label">Password</label>
                        <input type="password" name="password" id="password" required autofocus
                               class="ui-control">
                    </div>
                    <button type="submit"
                            class="ui-btn ui-btn-primary-solid w-full py-2.5">
                        Confirm
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
