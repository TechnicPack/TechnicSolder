@extends('layouts/master')
@section('title')
    <title>Modpack Management - TechnicSolder</title>
@stop
@section('content')
<h1>Modpack Management</h1>
<hr>

<div class="panel panel-default">
	<div class="panel-heading">
		<div class="pull-right">
			<a href="{{ URL::to('modpack/create') }}" class="btn btn-success btn-xs"><i class="icon-plus icon-white"></i>Create Modpack</a>
		</div>
	Modpack List
	</div>
	<div class="panel-body">
		@if (Session::has('success'))
		<div class="alert alert-success">
			{{ Session::get('success') }}
		</div>
		@endif
		@if ($errors->all())
		<div class="alert alert-danger">
		@foreach ($errors->all() as $error)
			{{ $error }}<br />
		@endforeach
		</div>
		@endif
		<div class="table-responsive">
		<table class="table table-striped table-bordered table-hover" id="dataTables">
			<thead>	
				<tr>
					<th>Name</th>
					<th>Slug</th>
					<th>Rec</th>
					<th>Latest</th>
					<th>Hidden</th>
					<th>Private</th>
					<th>Actions</th>
				</tr>
			</thead>
			<tbody>
			@foreach ($modpacks as $modpack)
				<tr>
					<td>{{ $modpack->name }}</td>
					<td>{{ $modpack->slug }}</td>
					<td>{{ !empty($modpack->recommended) ? $modpack->recommended : "N/A" }}</td>
					<td>{{ !empty($modpack->latest) ? $modpack->latest : "N/A" }}</td>
					<td>{{ $modpack->hidden == 1 ? "Yes" : "No" }}</td>
					<td>{{ $modpack->private == 1 ? "Yes" : "No" }}</td>
					<td>{{ HTML::link('modpack/view/'.$modpack->id, 'Manage Builds', array('class' => 'btn btn-warning btn-xs')) }} {{ HTML::link('modpack/edit/'.$modpack->id, 'Edit', array('class' => 'btn btn-primary btn-xs')) }} {{ HTML::link('modpack/delete/'.$modpack->id, 'Delete', array('class' => 'btn btn-danger btn-xs')) }}</td>
				</tr>
			@endforeach
			</tbody>
		</table>
		</div>
	</div>
</div>
@endsection
@section('bottom')
<script type="text/javascript">
$(document).ready(function() {
	$('#dataTables').dataTable();
});
</script>
@endsection