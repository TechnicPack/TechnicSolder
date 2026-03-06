@extends('layouts.master')
@section('title')
    <title>API Key Management - Technic Solder</title>
@stop
@section('content')
    <h1 class="text-2xl font-bold">API Key Management</h1>

    <div class="mt-6 bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-800">
        <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-800">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Delete API Key ({{ $key->name }})</h2>
        </div>
        <div class="px-5 py-4">
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                This will immediately remove access to Solder using this API Key. Make sure to unlink any packs using this key
                before doing this.
            </p>

            <form method="post" action="{{ url()->current() }}" accept-charset="UTF-8">
                @csrf
                <div class="flex items-center gap-3">
                    <button type="submit"
                            class="bg-red-600 hover:bg-red-700 text-white dark:bg-red-500/15 dark:text-red-400 dark:hover:bg-red-500/25 font-medium py-2 px-4 rounded-lg text-sm transition-colors">
                        Confirm Deletion
                    </button>
                    <a href="{{ url('/key/list') }}"
                       class="bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 font-medium py-2 px-4 rounded-lg text-sm transition-colors">
                        Go Back
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
