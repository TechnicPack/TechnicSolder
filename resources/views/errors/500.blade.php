<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Application Error - Technic Solder</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet">
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
               class="bg-blue-600 hover:bg-blue-700 text-white dark:bg-blue-500/15 dark:text-blue-400 dark:hover:bg-blue-500/25 font-medium py-2 px-4 rounded-lg text-sm transition-colors">
                Go Back
            </a>
            <a href="https://github.com/TechnicPack/TechnicSolder/issues" target="_blank" rel="noopener noreferrer"
               class="bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 font-medium py-2 px-4 rounded-lg text-sm transition-colors">
                Get Help
            </a>
        </div>
    </div>
</body>
</html>
