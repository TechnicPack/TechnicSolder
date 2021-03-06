@extends('layouts/master')
@section('title')
    <title>Client Management - Technic Solder</title>
@stop
@section('content')
<h1>Client Management</h1>
<hr>
<h2>Delete Client ({{ $client->name }})</h2>
<p>This will immediately remove access to all modpacks this user has access to.</p>
<form method="post" action="{{ URL::current() }}">
	<button type="submit" class="btn btn-danger">Confirm Deletion</button>
	{!! Html::link('client/list/', 'Go Back', ['class' => 'btn btn-primary']) !!}
</form>
@endsection