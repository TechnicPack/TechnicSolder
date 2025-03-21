@extends('layouts.master')
@section('title')
    <title>Create Modpack - Technic Solder</title>
@stop
@section('content')
    <div class="page-header">
        <h1>Modpack Management</h1>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            Create Modpack
        </div>
        <div class="panel-body">
            @include('partial.form-errors')
            <form action="{{ url()->current() }}" method="post" accept-charset="UTF-8">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">Modpack Name</label>
                            <input type="text" class="form-control" name="name" id="name">
                        </div>
                        <div class="form-group">
                            <label for="slug">Modpack Slug</label>
                            <input type="text" class="form-control" name="slug" id="slug">
                        </div>
                        <div class="form-group">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="hidden" id="hidden" checked>
                                    Hide modpack
                                </label>
                                <p class="help-block">Hidden modpacks will not show up in the API response for the
                                    modpack list. However, anyone with the modpack's slug can access all of its
                                    information.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <p>Creating a modpack is simple. Fill in the information here.</p>
                        <p>If you wish to link this modpack with an existing Technic Platform modpack, the slug must be
                            identical to your slug on the Platform!</p>
                    </div>
                </div>
                <input type="submit" class="btn btn-success" value="Add Modpack">
                <a href="{{ url('/modpack/list') }}" class="btn btn-primary">Go Back</a>
            </form>
        </div>
    </div>
    <script type="text/javascript">
        $("#slug").slugify('#name');
    </script>
@endsection