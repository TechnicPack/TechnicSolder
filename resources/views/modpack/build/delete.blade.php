@extends('layouts/master')
@section('title')
    <title>{{ $build->version }} - {{ $build->modpack->name }} - Technic Solder</title>
@stop
@section('content')
    <h1>Build Management</h1>
    <hr>
    <div class="panel panel-default">
        <div class="panel-heading">
            Delete request for build {{ $build->version }} ({{ $build->modpack->name }})
        </div>
        <div class="panel-body">
            <p>Are you sure you want to delete this build? This action is irreversible!</p>
            <form method="post" action="{{ url()->full() }}" accept-charset="UTF-8">
                @csrf
                <input type="hidden" name="confirm-delete" value="1">
                <input type="submit" class="btn btn-danger" value="Delete Build">
                <a href="{{ url('/modpack/view/'.$build->modpack->id) }}" class="btn btn-primary">Go Back</a>
            </form>
        </div>
    </div>
@endsection