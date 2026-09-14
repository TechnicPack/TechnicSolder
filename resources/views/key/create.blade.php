@extends('layouts.master')
@section('title')
    <title>Create Platform Key - Technic Solder</title>
@stop
@section('content')
    <h1 class="text-2xl font-bold">Platform Key Management</h1>

    <div class="ui-card mt-6">
        <div class="ui-card-header">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Add Platform Key</h2>
        </div>
        <div class="px-5 py-4">
            @include('partial.form-errors')

            <form action="{{ url()->current() }}" method="post" accept-charset="UTF-8" class="max-w-lg">
                @csrf
                <input type="hidden" name="add-key" value="1">

                <div class="mb-4">
                    <label for="name" class="ui-label">Name</label>
                    <input type="text"
                           name="name"
                           id="name"
                           class="ui-control">
                </div>

                <div class="mb-4">
                    <label for="api_key" class="ui-label">API Key</label>
                    <input type="text"
                           name="api_key"
                           id="api_key"
                           class="ui-control">
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit"
                            class="ui-btn ui-btn-primary">
                        Add Key
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
