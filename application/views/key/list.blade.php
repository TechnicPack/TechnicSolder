@layout('layouts/master')
@section('content')
<h1>API Key Management</h1>
<hr>
<div class="panel panel-default">
	<div class="panel-heading">
		<div class="pull-right">
		    <a href="{{ URL::to('key/create') }}" class="btn btn-success btn-xs"><i class="icon-plus icon-white"></i> Add API Key</a>
		</div>
	API Key List
	</div>
	<div class="panel-body">
		<p>This is the list of API keys that have access to Solder.</p>
		@if (Session::has('success'))
			<div class="alert alert-success">
				{{ Session::get('success') }}
			</div>
		@endif
		
		<div class="table-responsive">
		<table class="table table-striped table-bordered table-hover" id="dataTables">
		{{ Table::headers('#', 'Name', 'API Key', '') }}
		@foreach ($keys as $key)
			<tr>
				<td>{{ $key->id }}</td>
				<td>{{ $key->name }}</td>
				<td>{{ $key->api_key }}</td>
				<td>{{ HTML::link('key/delete/'.$key->id, 'Delete', array('class' => 'btn btn-danger btn-xs')) }}</td>
			</tr>
		@endforeach
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