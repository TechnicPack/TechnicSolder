@extends('layouts.master')
@section('title')
    <title>Create API Key - Technic Solder</title>
@stop
@section('content')
    <h1>API Key Management</h1>
    <hr>
    <div class="panel panel-default">
        <div class="panel-heading">
            Add API Key
        </div>
        <div class="panel-body">
            @if ($errors->all())
                <div class="alert alert-danger">
                    @foreach ($errors->all() as $error)
                        {{ $error }}<br/>
                    @endforeach
                </div>
            @endif
            <div class="row">
                <div class="col-md-6">
                    <form action="{{ url()->current() }}" method="post" accept-charset="UTF-8">
                        @csrf
                        <input type="hidden" name="add-key" value="1">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" name="name" id="name">
                        </div>
                        <div class="form-group">
                            <label for="api_key">API Key</label>
                            <input type="text" class="form-control" name="api_key" id="api_key">
                        </div>
                        <input type="submit" class="btn btn-success" value="Add Key">
                        <a href="{{ url('/key/list') }}" class="btn btn-primary">Go Back</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection