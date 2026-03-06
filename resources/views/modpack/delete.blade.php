@extends('layouts.master')
@section('title')
    <title>{{ $modpack->name }} - Technic Solder</title>
@stop
@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Modpack Management</h1>
    </div>

    <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-800">
        <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-800">
            <span class="font-semibold text-gray-900 dark:text-white">Delete Modpack: {{ $modpack->name }}</span>
        </div>
        <div class="p-5">
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-5">
                Deleting a modpack is irreversible. All associated builds will be immediately removed. This will remove them from your API. Users with this modpack already on their launcher will be able to continue to use it in "Offline Mode."
            </p>
            <form action="{{ url()->current() }}" method="post" accept-charset="UTF-8">
                @csrf
                <div class="flex items-center gap-3">
                    <button type="submit"
                            class="bg-red-600 hover:bg-red-700 text-white dark:bg-red-500/15 dark:text-red-400 dark:hover:bg-red-500/25 font-medium py-2 px-4 rounded-lg text-sm transition-colors">
                        Confirm Deletion
                    </button>
                    <a href="{{ url('/modpack/list') }}"
                       class="bg-blue-600 hover:bg-blue-700 text-white dark:bg-blue-500/15 dark:text-blue-400 dark:hover:bg-blue-500/25 font-medium py-2 px-4 rounded-lg text-sm transition-colors">
                        Go Back
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
