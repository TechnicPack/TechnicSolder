@layout('layouts/master')
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
		
		<div class="table-responsive">
		<table class="table table-striped table-bordered table-hover" id="dataTables">
		{{ Table::headers('#', 'Email', 'Username', 'Created', '') }}
		@foreach ($users as $user)
			<tr>
				<td>{{ $user->id }}</td>
				<td>{{ $user->email }}</td>
				<td>{{ $user->username }}</td>
				<td>{{ $user->created_at }}</td>
				<td>{{ HTML::link('user/edit/'.$user->id,'Edit', array('class' => 'btn btn-xs btn-warning')) }} {{ HTML::link('user/delete/'.$user->id, 'Delete', array('class' => 'btn btn-xs btn-danger')) }}</td>
			</tr>
		@endforeach
		</table>
		</div>
		@endsection
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