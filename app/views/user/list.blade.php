@extends('layouts/master')
@section('title')
    <title>User Management - TechnicSolder</title>
@stop
@section('content')
<div class="page-header">
<h1>User Management</h1>
</div>
<div class="panel panel-default">
	<div class="panel-heading">
	<div class="pull-right">
		    <a href="{{ URL::to('user/create') }}" class="btn btn-xs btn-success">Create User</a>
		</div>
	User List
	</div>
	<div class="panel-body">
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
					<th>ID #</th>
					<th>Email</th>
					<th>Username</th>
					<th>Updated by (User - IP)</th>
					<th>Updated at</th>
					<th>Actions</th>
				</tr>
			</thead>
			<tbody>
			@foreach ($users as $user)
				<tr>
					<td>{{ $user->id }}</td>
					<td>{{ $user->email }}</td>
					<td>{{ $user->username }}</td>
					<td>
					@if(!empty($user->updated_by_user_id))
						@if(User::find($user->updated_by_user_id))
							{{ User::find($user->updated_by_user_id)->username }}
						@else
							N/A
						@endif
					@endif
					 - {{ empty($user->updated_by_ip) ? "N/A" : $user->updated_by_ip }}
					</td>
					<td>{{ date_format($user->updated_at, 'M-d-Y g:ia') }}</td>
					<td>{{ HTML::link('user/edit/'.$user->id,'Edit', array('class' => 'btn btn-xs btn-warning')) }} {{ HTML::link('user/delete/'.$user->id, 'Delete', array('class' => 'btn btn-xs btn-danger')) }}</td>
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