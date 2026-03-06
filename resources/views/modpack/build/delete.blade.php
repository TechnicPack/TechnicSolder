@extends('layouts.master')
@section('title')
    <title>{{ $build->version }} - {{ $build->modpack->name }} - Technic Solder</title>
@stop
@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Build Management</h1>
        <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Delete build {{ $build->version }}</p>
    </div>

    <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-800">
        <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-800">
            <span class="font-semibold text-gray-900 dark:text-white">Delete request for build {{ $build->version }} ({{ $build->modpack->name }})</span>
        </div>
        <div class="p-5">
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Are you sure you want to delete this build? This action is irreversible!</p>
            <form method="post" action="{{ url('/modpack/build/'.$build->id.'/delete') }}" accept-charset="UTF-8">
                @csrf
                <div class="flex items-center gap-3">
                    <button type="submit"
                            class="bg-red-600 hover:bg-red-700 text-white dark:bg-red-500/15 dark:text-red-400 dark:hover:bg-red-500/25 font-medium py-2 px-4 rounded-lg text-sm transition-colors">
                        Delete Build
                    </button>
                    <a href="{{ url('/modpack/view/'.$build->modpack->id) }}"
                       class="bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 font-medium py-2 px-4 rounded-lg text-sm transition-colors">
                        Go Back
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
