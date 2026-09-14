@extends('layouts.master')
@section('title')
    <title>{{ $build->version }} - {{ $build->modpack->name }} - Technic Solder</title>
@stop
@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Build Management</h1>
        <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Delete build {{ $build->version }}</p>
    </div>

    <div class="ui-card">
        <div class="ui-card-header">
            <span class="font-semibold text-gray-900 dark:text-white">Delete request for build {{ $build->version }} ({{ $build->modpack->name }})</span>
        </div>
        <div class="p-5">
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Are you sure you want to delete this build? This action is irreversible!</p>
            <form method="post" action="{{ url('/modpack/build/'.$build->id.'/delete') }}" accept-charset="UTF-8">
                @csrf
                <div class="flex items-center gap-3">
                    <button type="submit"
                            class="ui-btn ui-btn-danger">
                        Delete Build
                    </button>
                    <a href="{{ url('/modpack/view/'.$build->modpack->id) }}"
                       class="ui-btn ui-btn-secondary">
                        Go Back
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
