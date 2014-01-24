@layout('layouts/master')
@section('content')
<div class="page-header">
<h1>Mod Library</h1>
</div>
<div class="panel panel-default">
	<div class="panel-heading">
	Mod List
	</div>
	<div class="panel-body">
		@if (Session::has('deleted'))
		<div class="alert alert-error">
			{{ Session::get('deleted') }}
		</div>
		@endif
		<table class="table table-striped table-bordered table-hover" id="dataTables">
		{{ Table::headers('#','Mod Name', 'Author', '') }}
		@foreach ($mods as $mod)
			<tr>
				<td>{{ HTML::link('mod/view/'.$mod->id, $mod->id) }}</td>
				@if (!empty($mod->pretty_name))
					<td>{{ $mod->pretty_name }} ({{ $mod->name }})</td>
				@else
					<td>{{ $mod->name }}</td>
				@endif
				<td>{{ $mod->author }}</td>
				<td>{{ HTML::link('mod/view/'.$mod->id,'Manage', array("class" => "btn btn-sm btn-primary")) }}</td>
			</tr>
		@endforeach
		</table>
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