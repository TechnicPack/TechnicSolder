@extends('layouts.master')
@section('title')
    <title>Clone Modpack - Technic Solder</title>
@stop
@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Modpack Management</h1>
        <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Clone an existing modpack</p>
    </div>

    <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-800">
        <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-800">
            <span class="font-semibold text-gray-900 dark:text-white">Clone Modpack</span>
        </div>
        <div class="p-5">
            @include('partial.form-errors')
            <div class="mb-5 p-4 rounded-lg bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 text-sm text-blue-700 dark:text-blue-300">
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
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Modpack Name</label>
                        <input type="text"
                               name="name"
                               id="name"
                               x-model="name"
                               @input="slug = window.slugify(name)"
                               class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors">
                    </div>
                    <div>
                        <label for="slug" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Modpack Slug</label>
                        <input type="text"
                               name="slug"
                               id="slug"
                               x-model="slug"
                               class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors">
                    </div>
                    <div>
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="hidden" id="hidden" checked
                                   class="rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500 dark:bg-gray-800">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Hide modpack</span>
                        </label>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Hidden modpacks will not show up in the API response for the modpack list. However, anyone with the modpack's slug can access all of its information.
                        </p>
                    </div>
                </div>
                <div class="mt-6 flex items-center gap-3">
                    <button type="submit"
                            class="bg-green-600 hover:bg-green-700 text-white dark:bg-green-500/15 dark:text-green-400 dark:hover:bg-green-500/25 font-medium py-2 px-4 rounded-lg text-sm transition-colors">
                        Clone Modpack
                    </button>
                    <a href="{{ url('/modpack/view/' . $modpack->id) }}"
                       class="bg-blue-600 hover:bg-blue-700 text-white dark:bg-blue-500/15 dark:text-blue-400 dark:hover:bg-blue-500/25 font-medium py-2 px-4 rounded-lg text-sm transition-colors">
                        Go Back
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
