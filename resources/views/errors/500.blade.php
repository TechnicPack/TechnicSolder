<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Application Error - Technic Solder</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    @fonts
    @include('partial.dark-mode-script')
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gray-50 dark:bg-gray-950 min-h-screen flex items-center justify-center p-4">
    <div class="text-center max-w-md">
        <p class="text-7xl font-bold text-gray-200 dark:text-gray-800">500</p>
        <h1 class="mt-4 text-2xl font-bold text-gray-900 dark:text-white">Something went wrong</h1>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">{{ config('app.debug') ? $exception->getMessage() : 'An unexpected error occurred. Please try again later.' }}</p>
        <div class="mt-6 flex items-center justify-center gap-3">
            <a href="{{ URL::previous() }}"
               class="ui-btn ui-btn-primary">
                Go Back
            </a>
            <a href="https://github.com/TechnicPack/TechnicSolder/issues" target="_blank" rel="noopener noreferrer"
               class="ui-btn ui-btn-secondary">
                Get Help
            </a>
        </div>
    </div>
</body>
</html>
