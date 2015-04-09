@extends('layouts/master')
@section('title')
    <title>{{ $build->version }} - {{ $build->modpack->name }} - TechnicSolder</title>
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
		<form method="post" action="{{ URL::full() }}">
			<input type="hidden" name="confirm-delete" value="1">
			{{ Form::submit('Delete Build', array('class' => 'btn btn-danger')) }}
			{{ HTML::link('modpack/view/'.$build->modpack->id, 'Go Back', array('class' => 'btn btn-primary')) }}
		</form>
	</div>
</div>
@endsection