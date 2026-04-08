@extends('layouts.master')
@section('title')
    <title>Create Mod - Technic Solder</title>
@stop
@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold">Mod Library</h1>
    </div>

    <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-800">
        <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-800">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Add Mod</h2>
        </div>
        <div class="p-5">
            @include('partial.form-errors')

            <form method="post"
                  action="{{ url('/mod/create') }}"
                  accept-charset="UTF-8"
                  autocomplete="off"
                  x-data="{
                      prettyName: '',
                      slug: '',
                      slugManual: false,
                      updateSlug() {
                          if (!this.slugManual) {
                              this.slug = window.slugify(this.prettyName);
                          }
                      },
                      onSlugInput() {
                          if (this.slug === '') {
                              this.slugManual = false;
                              this.updateSlug();
                          } else {
                              this.slugManual = true;
                          }
                      },
                      get slugPreview() {
                          return this.slug || '[slug]';
                      }
                  }">
                @csrf
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <div class="space-y-4">
                        <div>
                            <label for="pretty_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Pretty Name</label>
                            <input type="text"
                                   name="pretty_name"
                                   id="pretty_name"
                                   x-model="prettyName"
                                   @input="updateSlug()"
                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors">
                        </div>
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Slug</label>
                            <input type="text"
                                   name="name"
                                   id="name"
                                   x-model="slug"
                                   @input="onSlugInput()"
                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors">
                        </div>
                        <div>
                            <label for="author" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Author</label>
                            <input type="text"
                                   name="author"
                                   id="author"
                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors">
                        </div>
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                            <textarea name="description"
                                      id="description"
                                      rows="5"
                                      class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors"></textarea>
                        </div>
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Notes</label>
                            <textarea name="notes"
                                      id="notes"
                                      rows="3"
                                      placeholder="Private notes (not shown in API)"
                                      class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors"></textarea>
                        </div>
                        <div>
                            <label for="link" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Website</label>
                            <input type="text"
                                   name="link"
                                   id="link"
                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-colors">
                        </div>
                    </div>
                    <div>
                        <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700">
                            <p class="text-sm text-gray-700 dark:text-gray-300 mb-3">
                                Because Solder doesn't do any file handling yet you will need to manually manage your set of
                                mods in your repository. The mod repository structure is very strict and must match your
                                Solder data exactly. An example of your mod directory structure will be listed below:
                            </p>
                            <div class="bg-gray-100 dark:bg-gray-800 rounded-lg p-3 font-mono text-sm text-gray-800 dark:text-gray-200 mb-3">
                                <div>mods/<span x-text="slugPreview" class="text-blue-600 dark:text-blue-400"></span>/</div>
                                <div>mods/<span x-text="slugPreview" class="text-blue-600 dark:text-blue-400"></span>/<span x-text="slugPreview" class="text-blue-600 dark:text-blue-400"></span>-[version].zip</div>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                The mod slug automatically updates based on the mod name. You can change the slug to whatever
                                you want after you set the name. If you modify the slug, it will no longer update
                                automatically. If you wish to restore that behavior, simply empty the slug field.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="mt-6 flex items-center gap-3">
                    <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white dark:bg-blue-500/15 dark:text-blue-400 dark:hover:bg-blue-500/25 font-medium py-2 px-4 rounded-lg text-sm transition-colors">
                        Add Mod
                    </button>
                    <a href="{{ url('/mod/list') }}"
                       class="bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 font-medium py-2 px-4 rounded-lg text-sm transition-colors">
                        Go Back
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
