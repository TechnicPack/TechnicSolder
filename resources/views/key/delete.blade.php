@extends('layouts.master')
@section('title')
    <title>Platform Key Management - Technic Solder</title>
@stop
@section('content')
    <h1 class="text-2xl font-bold">Platform Key Management</h1>

    <div class="ui-card mt-6">
        <div class="ui-card-header">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Delete Platform Key ({{ $key->name }})</h2>
        </div>
        <div class="px-5 py-4">
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                This will immediately remove access to Solder using this platform key. Make sure to unlink any packs using this key
                before doing this.
            </p>

            <form method="post" action="{{ url()->current() }}" accept-charset="UTF-8">
                @csrf
                <div class="flex items-center gap-3">
                    <button type="submit"
                            class="ui-btn ui-btn-danger">
                        Confirm Deletion
                    </button>
                    <a href="{{ url('/key/list') }}"
                       class="ui-btn ui-btn-secondary">
                        Go Back
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
