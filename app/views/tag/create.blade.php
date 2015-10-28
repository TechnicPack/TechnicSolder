@extends('layouts/master')
@section('title')
    <title>Create Tag - TechnicSolder</title>
@stop
@section('content')
    <div class="page-header">
        <h1>Tag Library</h1>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            Add Tag
        </div>
        <div class="panel-body">
            @if ($errors->all())
                <div class="alert alert-danger">
                    @foreach ($errors->all() as $error)
                        {{ $error }}<br />
                    @endforeach
                </div>
            @endif
            <form method="post" action="{{ URL::to('tag/create') }}">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="pretty_name">Tag Name</label>
                            <input type="text" class="form-control" name="pretty_name" id="pretty_name">
                        </div>
                        <div class="form-group">
                            <label for="name">Tag Slug</label>
                            <input type="text" class="form-control" name="name" id="name">
                        </div>
                    </div>
                </div>
                {{ Form::submit('Add Tag', array('class' => 'btn btn-success')) }}
                {{ HTML::link('tag/list/', 'Go Back', array('class' => 'btn btn-primary')) }}
            </form>
        </div>
    </div>
@endsection
@section('bottom')
    <script type="text/javascript">
        $("#name").slugify('#pretty_name');
        $(".modslug").slugify("#pretty_name");
        $("#name").keyup(function() {
            $(".modslug").html($(this).val());
        });
    </script>
@endsection