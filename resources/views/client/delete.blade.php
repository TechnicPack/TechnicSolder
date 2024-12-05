@extends('layouts.master')
@section('title')
    <title>Client Management - Technic Solder</title>
@stop
@section('content')
    <h1>Client Management</h1>
    <hr>
    <h2>Delete Client ({{ $client->name }})</h2>
    <p>This will immediately remove access to all modpacks this user has access to.</p>
    <form method="post" action="{{ url()->current() }}" accept-charset="UTF-8">
        @csrf
        <input type="submit" class="btn btn-danger" value="Confirm Deletion">
        <a href="{{ url('/client/list') }}" class="btn btn-primary">Go Back</a>
    </form>
@endsection