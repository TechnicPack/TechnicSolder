<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Reset Password &middot; Technic Solder</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    @fonts
    @include('partial.dark-mode-script')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 dark:bg-gray-950 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-sm" x-data>
        <div class="text-center mb-8">
            <img src="{{ asset('img/wrenchIcon.svg') }}" alt="Solder" class="h-16 mx-auto mb-4">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Reset Password</h1>
        </div>

        <div class="ui-card p-6">
            @if (session('status'))
                <div class="ui-alert ui-alert-success mb-4 p-3">
                    {{ session('status') }}
                </div>
            @endif

            @error('email')
                <div class="ui-alert ui-alert-danger mb-4 p-3">
                    {{ $message }}
                </div>
            @enderror

            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                Enter your email address and we'll send you a link to reset your password.
            </p>

            <form method="post" action="{{ route('password.email') }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label for="email" class="ui-label">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                               class="ui-control"
                               placeholder="admin@example.com">
                    </div>
                    <button type="submit"
                            class="ui-btn ui-btn-primary-solid w-full py-2.5">
                        Send Reset Link
                    </button>
                </div>
            </form>

            <div class="mt-4 text-center">
                <a href="{{ route('login') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">Back to Sign in</a>
            </div>
        </div>
    </div>
</body>
</html>
