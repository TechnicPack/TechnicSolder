@extends('layouts.master')
@section('title')
    <title>Clone Modpack - Technic Solder</title>
@stop
@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Modpack Management</h1>
        <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Clone an existing modpack</p>
    </div>

    <div class="ui-card">
        <div class="ui-card-header">
            <span class="font-semibold text-gray-900 dark:text-white">Clone Modpack</span>
        </div>
        <div class="p-5">
            @include('partial.form-errors')
            <div class="ui-alert ui-alert-info mb-5 p-4">
                Cloning <strong>{{ $modpack->name }}</strong> with {{ $modpack->builds->count() }} {{ Str::plural('build', $modpack->builds->count()) }} and all mod assignments.
            </div>
            <form action="{{ url('modpack/clone/' . $modpack->id) }}" method="post" accept-charset="UTF-8"
                  x-data="{ name: '{{ $modpack->name }} (Copy)', slug: '{{ $modpack->slug }}-copy' }">
                @csrf
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-5">
                    If you wish to link this modpack with an existing Technic Platform modpack, the slug must be identical to your slug on the Platform.
                </p>
                <div class="space-y-5">
                    <div>
                        <label for="name" class="ui-label">Modpack Name</label>
                        <input type="text"
                               name="name"
                               id="name"
                               x-model="name"
                               @input="slug = window.slugify(name)"
                               class="ui-control">
                    </div>
                    <div>
                        <label for="slug" class="ui-label">Modpack Slug</label>
                        <input type="text"
                               name="slug"
                               id="slug"
                               x-model="slug"
                               class="ui-control">
                    </div>
                    <div>
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="hidden" id="hidden" checked
                                   class="ui-checkbox dark:bg-gray-800">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Hide modpack</span>
                        </label>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Hidden modpacks will not show up in the API response for the modpack list. However, anyone with the modpack's slug can access all of its information.
                        </p>
                    </div>
                </div>
                <div class="mt-6 flex items-center gap-3">
                    <button type="submit"
                            class="ui-btn ui-btn-success">
                        Clone Modpack
                    </button>
                    <a href="{{ url('/modpack/view/' . $modpack->id) }}"
                       class="ui-btn ui-btn-primary">
                        Go Back
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
