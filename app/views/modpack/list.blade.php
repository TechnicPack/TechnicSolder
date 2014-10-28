@extends('layouts/master')
@section('content')
<h1>Modpack Management</h1>
<hr>
@if (Session::has('deleted'))
<div class="alert alert-error">
	{{ Session::get('deleted') }}
</div>
@endif
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
					<td>{{ $modpack->recommended }}</td>
					<td>{{ $modpack->latest }}</td>
					<td>{{ $modpack->hidden }}</td>
					<td>{{ $modpack->private }}</td>
					<td>{{ HTML::link('modpack/build/'.$modpack->id, 'Manage Builds', array('class' => 'btn btn-warning btn-xs')) }} {{ HTML::link('modpack/edit/'.$modpack->id, 'Edit', array('class' => 'btn btn-primary btn-xs')) }} {{ HTML::link('modpack/delete/'.$modpack->id, 'Delete', array('class' => 'btn btn-danger btn-xs')) }}</td>
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