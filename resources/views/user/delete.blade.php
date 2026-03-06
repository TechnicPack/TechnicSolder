@extends('layouts.master')
@section('title')
    <title>Delete User - Technic Solder</title>
@stop
@section('content')
    <h1 class="text-2xl font-bold">User Management</h1>

    <div class="mt-6 bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-800">
        <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-800">
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
                <div class="mb-4 p-4 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-sm text-red-700 dark:text-red-300">
                    You are about to delete yourself. If you do this, you will no longer be able to access Solder.
                </div>
            @endif

            <form method="post" action="{{ url()->current() }}" accept-charset="UTF-8">
                @csrf
                <div class="flex items-center gap-3">
                    <button type="submit"
                            class="bg-red-600 hover:bg-red-700 text-white dark:bg-red-500/15 dark:text-red-400 dark:hover:bg-red-500/25 font-medium py-2 px-4 rounded-lg text-sm transition-colors">
                        Confirm Deletion
                    </button>
                    <a href="{{ url('/user/list') }}"
                       class="bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 font-medium py-2 px-4 rounded-lg text-sm transition-colors">
                        Go Back
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
