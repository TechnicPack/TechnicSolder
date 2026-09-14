@extends('layouts.master')
@section('title')
    <title>Delete User - Technic Solder</title>
@stop
@section('content')
    <h1 class="text-2xl font-bold">User Management</h1>

    <div class="ui-card mt-6">
        <div class="ui-card-header">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                Delete User ({{ $user->username }})
                @if(Auth::user()->id == $user->id)
                    <span class="text-yellow-600 dark:text-yellow-400 italic ml-1">That's you!</span>
                @endif
            </h2>
        </div>
        <div class="px-5 py-4">
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                This will immediately remove the user from Solder.<br>
                Are you sure you want to remove <strong class="text-gray-900 dark:text-gray-100">{{ $user->username }}</strong>?
            </p>

            @if(Auth::user()->id == $user->id)
                <div class="ui-alert ui-alert-danger mb-4 p-4">
                    You are about to delete yourself. If you do this, you will no longer be able to access Solder.
                </div>
            @endif

            <form method="post" action="{{ url()->current() }}" accept-charset="UTF-8">
                @csrf
                <div class="flex items-center gap-3">
                    <button type="submit"
                            class="ui-btn ui-btn-danger">
                        Confirm Deletion
                    </button>
                    <a href="{{ url('/user/list') }}"
                       class="ui-btn ui-btn-secondary">
                        Go Back
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
