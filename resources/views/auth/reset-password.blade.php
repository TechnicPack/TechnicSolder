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
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Set New Password</h1>
        </div>

        <div class="ui-card p-6">
            @if ($errors->any())
                <div class="ui-alert ui-alert-danger mb-4 p-3">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="post" action="{{ route('password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <div class="space-y-4">
                    <div>
                        <label for="email" class="ui-label">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $request->email) }}" required autofocus
                               class="ui-control">
                    </div>
                    <div>
                        <label for="password" class="ui-label">New Password</label>
                        <input type="password" name="password" id="password" required
                               class="ui-control">
                    </div>
                    <div>
                        <label for="password_confirmation" class="ui-label">Confirm Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" required
                               class="ui-control">
                    </div>
                    <button type="submit"
                            class="ui-btn ui-btn-primary-solid w-full py-2.5">
                        Reset Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
