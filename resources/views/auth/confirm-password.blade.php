<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Confirm Password &middot; Technic Solder</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet">
    @include('partial.dark-mode-script')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 dark:bg-gray-950 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-sm">
        <div class="text-center mb-8">
            <img src="{{ asset('img/wrenchIcon.svg') }}" alt="Solder" class="h-16 mx-auto mb-4">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Confirm Password</h1>
        </div>

        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-800 p-6">
            @error('password')
                <div class="mb-4 p-3 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-sm text-red-700 dark:text-red-300">
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
                        <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Password</label>
                        <input type="password" name="password" id="password" required autofocus
                               class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors">
                    </div>
                    <button type="submit"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 px-4 rounded-lg text-sm transition-colors">
                        Confirm
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
