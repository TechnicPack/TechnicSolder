@extends('layouts.master')
@section('title')
    <title>{{ $modpack->name }} - Technic Solder</title>
@stop
@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Modpack Management</h1>
    </div>

    <div class="ui-card">
        <div class="ui-card-header">
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
                            class="ui-btn ui-btn-danger">
                        Confirm Deletion
                    </button>
                    <a href="{{ url('/modpack/list') }}"
                       class="ui-btn ui-btn-primary">
                        Go Back
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
