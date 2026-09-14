@extends('layouts.master')
@section('title')
    <title>Create Client - Technic Solder</title>
@stop
@section('content')
    <h1 class="text-2xl font-bold">Client Management</h1>

    <div class="ui-card mt-6">
        <div class="ui-card-header">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Add Client</h2>
        </div>
        <div class="px-5 py-4">
            @include('partial.form-errors')

            <form method="post" action="{{ url()->current() }}" accept-charset="UTF-8" class="max-w-lg">
                @csrf
                <input type="hidden" name="add-client" value="1">

                <div class="mb-4">
                    <label for="name" class="ui-label">Name</label>
                    <input type="text"
                           name="name"
                           id="name"
                           class="ui-control">
                </div>

                <div class="mb-4">
                    <label for="uuid" class="ui-label">UUID</label>
                    <input type="text"
                           name="uuid"
                           id="uuid"
                           class="ui-control">
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit"
                            class="ui-btn ui-btn-primary">
                        Add Client
                    </button>
                    <a href="{{ url('/client/list') }}"
                       class="ui-btn ui-btn-secondary">
                        Go Back
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
