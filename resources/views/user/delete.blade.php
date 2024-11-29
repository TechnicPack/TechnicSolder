@extends('layouts/master')
@section('title')
    <title>Delete User - Technic Solder</title>
@stop
@section('content')
    <div class="page-header">
        <h1>User Management</h1>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 style="margin-top:10px;">Delete User
                ({{ $user->username }} {{ Auth::user()->id == $user->id ? "<i class='text-warning'>That's you!</i>" : "" }}
                )</h3>
        </div>
        <div class="panel-body">
            <p>This will immediately remove the user from Solder.<br>Are you sure you want to remove
                <strong>{{ $user->username }}</strong>?</p>
            @if(Auth::user()->id == $user->id)
                <p class="alert alert-danger" style="display: inline-block;">You are about to delete yourself. If you do
                    this, you will no longer be able to access Solder.</p>
            @endif
            <form method="post" action="{{ url()->current() }}" accept-charset="UTF-8">
                @csrf
                <input type="submit" class="btn btn-danger" value="Confirm Deletion">
                <a href="{{ url('/user/list') }}" class="btn btn-primary">Go Back</a>
            </form>
        </div>
    </div>
@endsection