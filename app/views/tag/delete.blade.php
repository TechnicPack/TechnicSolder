@extends('layouts/master')
@section('title')
    <title>Tag Management - TechnicSolder</title>
@stop
@section('content')
    <div class="page-header">
        <h1>Tag Library</h1>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            Delete Request for {{ $tag->name }}
        </div>
        <div class="panel-body">
            {{ Form::open() }}
            {{ Form::submit('Delete Tag', array('class' => 'btn btn-danger')) }}
            {{ HTML::link('tag/list/', 'Go Back', array('class' => 'btn btn-primary')) }}
            {{ Form::close() }}
        </div>
    </div>
@endsection