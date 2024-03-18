@extends('layouts/master')
@section('title')
    <title>Mod Library - Technic Solder</title>
@stop
@section('content')
<div class="page-header">
<h1>Mod Library</h1>
</div>
<div class="panel panel-default">
	<div class="panel-heading">
	<div class="pull-right">
		<a href="{{ URL::to('mod/create') }}" class="btn btn-xs btn-success">Add Mod</a>
		<a href="{{ URL::to('mod/import') }}" class="btn btn-xs btn-success">Import Mod</a>
	</div>
	Mod List
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
		<table class="table table-striped table-bordered table-hover" id="dataTables">
			<thead>
				<tr>
					<th>#</th>
					<th>Mod Name</th>
					<th>Author</th>
					<th>Website</th>
					<th>Actions</th>
				</tr>
			</thead>
			<tbody>
			@foreach ($mods as $mod)
				<tr>
					<td>{!! Html::link('mod/view/'.$mod->id, $mod->id) !!}</td>
					<td>
						@if (!empty($mod->pretty_name))
							{!! Html::link('mod/view/'.$mod->id, $mod->pretty_name) !!} ({{ $mod->name }})
						@else
							{!! Html::link('mod/view/'.$mod->id, $mod->name) !!}
						@endif
						<br/>
						<b>Latest Version:</b> {{ !$mod->versions->isEmpty() ? $mod->versions->first()->version : "N/A" }}
					</td>
					<td>{{ !empty($mod->author) ? $mod->author : "N/A" }}</td>
					<td>{!! !empty($mod->link) ? Html::link($mod->link, $mod->link, ["target" => "_blank"]) : "N/A" !!}</td>
					<td>{!! Html::link('mod/view/'.$mod->id,'Manage', ["class" => "btn btn-xs btn-primary"]) !!}</td>
				</tr>
			@endforeach
		</table>
	</div>
</div>
@endsection
@section('bottom')
<script type="text/javascript">
$(document).ready(function() {
	$('#dataTables').dataTable({
		"order": [[ 1, "asc" ]]
	});

});
</script>
@endsection
