@extends('layouts.master')
@include('partial.slugify')
@section('title')
    <title>Create Mod - Technic Solder</title>
@stop
@section('content')
    <div class="page-header">
        <h1>Mod Library</h1>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            Add Mod
        </div>
        <div class="panel-body">
            @if ($errors->all())
                <div class="alert alert-danger">
                    @foreach ($errors->all() as $error)
                        {{ $error }}<br/>
                    @endforeach
                </div>
            @endif
            <form method="post" action="{{ url('/mod/create') }}" accept-charset="UTF-8" autocomplete="off">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="pretty_name">Mod Name</label>
                            <input type="text" class="form-control" name="pretty_name" id="pretty_name">
                        </div>
                        <div class="form-group">
                            <label for="name">Mod Slug</label>
                            <input type="text" class="form-control" name="name" id="name">
                        </div>
                        <div class="form-group">
                            <label for="author">Author</label>
                            <input type="text" class="form-control" name="author" id="author">
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea name="description" id="description" class="form-control" rows="5"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="link">Mod Website</label>
                            <input type="text" class="form-control" name="link" id="link">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <p>Because Solder doesn't do any file handling yet you will need to manually manage your set of
                            mods in your repository. The mod repository structure is very strict and must match your
                            Solder data exactly. An example of your mod directory structure will be listed below:</p>
                        <blockquote>
                            <samp>/mods/<span class="mod-slug">[slug]</span>/<br>
                            /mods/<span class="mod-slug">[slug]</span>/<span class="mod-slug">[slug]</span>-[version].zip</samp>
                        </blockquote>
                        <p>The mod slug automatically updates based on the mod name. You can change the slug to whatever
                            you want after you set the name. If you modify the slug, it will no longer update
                            automatically. If you wish to restore that behavior, then simply empty the slug field.</p>
                    </div>
                </div>
                <input type="submit" class="btn btn-success" value="Add Mod">
                <a href="{{ url('/mod/list') }}" class="btn btn-primary">Go Back</a>
            </form>
        </div>
    </div>
@endsection
@section('bottom')
    <script>
        const slugInputField = $('#name');
        const slugSpans = $('.mod-slug');

        slugInputField.slugify('#pretty_name');
        slugInputField.on('input', function () {
            slugSpans.text($(this).val() || '[slug]');
        });

        slugSpans.slugify('#pretty_name', {
            postSlug: function (slug) {
                if (slug == null || slug === '') {
                    return '[slug]';
                }

                return slug;
            }
        });
    </script>
@endsection