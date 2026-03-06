@extends('layouts.master')
@section('title')
    <title>Create Client - Technic Solder</title>
@stop
@section('content')
    <h1 class="text-2xl font-bold">Client Management</h1>

    <div class="mt-6 bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-800">
        <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-800">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Add Client</h2>
        </div>
        <div class="px-5 py-4">
            @include('partial.form-errors')

            <form method="post" action="{{ url()->current() }}" accept-charset="UTF-8" class="max-w-lg">
                @csrf
                <input type="hidden" name="add-client" value="1">

                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Name</label>
                    <input type="text"
                           name="name"
                           id="name"
                           class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors">
                </div>

                <div class="mb-4">
                    <label for="uuid" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">UUID</label>
                    <input type="text"
                           name="uuid"
                           id="uuid"
                           class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors">
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white dark:bg-blue-500/15 dark:text-blue-400 dark:hover:bg-blue-500/25 font-medium py-2 px-4 rounded-lg text-sm transition-colors">
                        Add Client
                    </button>
                    <a href="{{ url('/client/list') }}"
                       class="bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 font-medium py-2 px-4 rounded-lg text-sm transition-colors">
                        Go Back
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
