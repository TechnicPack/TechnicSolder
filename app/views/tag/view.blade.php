@extends('layouts/master')
@section('title')
    <title>{{ empty($tag->pretty_name) ? $tag->name : $tag->pretty_name }} - TechnicSolder</title>
@stop
@section('content')
    <div class="page-header">
        <h1>Tag Library</h1>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            @if (!empty($tag->pretty_name))
                {{ $tag->pretty_name }}
                <small>{{ $tag->name }}</small>
            @else
                {{ $tag->name }}
            @endif
        </div>
        <div class="panel-body">
            @if ($errors->all())
                <div class="alert alert-danger">
                    @foreach ($errors->all() as $error)
                        {{ $error }}<br/>
                    @endforeach
                </div>
            @endif
            @if (Session::has('success'))
                <div class="alert alert-success">
                    {{ Session::get('success') }}
                </div>
            @endif
            <form method="post" action="{{ URL::to('tag/modify/'.$tag->id) }}">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="pretty_name">Tag Name</label>
                            <input type="text" class="form-control" name="pretty_name" id="pretty_name"
                                   value="{{ $tag->pretty_name }}">
                        </div>
                        <div class="form-group">
                            <label for="name">Tag Slug</label>
                            <input type="text" class="form-control" name="name" id="name" value="{{ $tag->name }}">
                        </div>
                    </div>
                </div>
                {{ Form::submit('Save Changes', array('class' => 'btn btn-success')) }}
                {{ HTML::link('tag/delete/'.$tag->id, 'Delete Mod', array('class' => 'btn btn-danger')) }}
                {{ HTML::link('tag/list/', 'Go Back', array('class' => 'btn btn-primary')) }}
            </form>
        </div>
    </div>
@endsection