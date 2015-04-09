@extends('layouts/master')
@section('title')
    <title>Client Management - TechnicSolder</title>
@stop
@section('content')
<h1>Client Management</h1>
<hr>
<div class="panel panel-default">
	<div class="panel-heading">
		<div class="pull-right">
		    <a href="{{ URL::to('client/create') }}" class="btn btn-success btn-xs"><i class="icon-plus icon-white"></i> Add Client</a>
		</div>
	Client List
	</div>
	<div class="panel-body">
		<p>This is the client management area. Here you can register your launcher client UUID to Solder so that private builds will show up to you in the launcher. After a client is added to this list, they need to be linked to the modpacks you want them to have access to in Solder.</p>
		@if (Session::has('success'))
			<div class="alert alert-success">
				{{ Session::get('success') }}
			</div>
		@endif
		<div class="table-responsive">
		<table class="table table-striped table-bordered table-hover" id="dataTables">
			<thead>	
				<tr>
					<th>#</th>
					<th>Name</th>
					<th>Client UUID</th>
					<th>Actions</th>
				</tr>
			</thead>
			<tbody>
			@foreach ($clients as $client)
				<tr>
					<td>{{ $client->id }}</td>
					<td>{{ $client->name }}</td>
					<td>{{ $client->uuid }}</td>
					<td>{{ HTML::link('client/delete/'.$client->id, 'Delete', array('class' => 'btn btn-danger btn-xs')) }}</td>
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