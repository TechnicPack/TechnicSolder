@layout('layouts/master')
@section('content')
<h1>Modpack Management</h1>
<hr>
<div class="panel panel-default">
	<div class="panel-heading">
		<div class="pull-right">
			<a class="btn btn-primary btn-xs" href="{{ URL::to('modpack/addbuild/'.$modpack->id) }}">Create New Build</a> 
			<a class="btn btn-warning btn-xs" href="{{ URL::to('modpack/edit/'.$modpack->id) }}">Edit Modpack</a>
		</div>
	{{ $modpack->name }}
	</div>
	<div class="panel-body">
		<div class="alert alert-success" id="success-ajax" style="width: 100%;display: none">
		</div>
		@if (Session::has('success'))
		<div class="alert alert-success">
			{{ Session::get('success') }}
		</div>
		@endif
		@if (Session::has('deleted'))
		<div class="alert alert-error">
			{{ Session::get('deleted') }}
		</div>
		@endif
		<div class="table-responsive">
		<table class="table table-striped table-bordered table-hover" id="dataTables">
		{{ Table::headers('#', 'Build Number', 'MC Version', 'Mod Count', 'Rec', 'Latest', 'Published', 'Private', 'Created', '') }}
		@foreach ($modpack->builds()->order_by('id', 'desc')->get() as $build)
			<tr>
				<td>{{ $build->id }}</td>
				<td>{{ $build->version }}</td>
				<td>{{ $build->minecraft }}</td>
				<td>{{ count($build->modversions) }}</td>
				<td><input type="radio" name="recommended" value="{{ $build->version }}"{{ $checked = ($modpack->recommended == $build->version ? " checked" : "") }}></td>
				<td><input type="radio" name="latest" value="{{ $build->version }}"{{ $checked = ($modpack->latest == $build->version ? " checked" : "") }}></td>
				<td><input type="checkbox" name="published" value="1" class="published" rel="{{ $build->id }}"{{ ($build->is_published ? " checked" : "") }}></td>
				<td><input type="checkbox" name="private" value="1" class="private" rel="{{ $build->id }}"{{ ($build->private ? " checked" : "") }}></td>
				<td>{{ $build->created_at }}</td>
				<td>{{ HTML::link('modpack/build/'.$build->id, "Manage",'class="btn btn-xs btn-primary"') }} {{ HTML::link('modpack/build/'.$build->id.'?action=delete', "Delete",'class="btn btn-xs btn-danger"') }}</td>
			</tr>
		@endforeach
		</table>
		</div>
	</div>
</div>
@endsection
@section('bottom')
<script type="text/javascript">

$("input[name=recommended]").change(function() {
	$.ajax({
		type: "GET",
		url: "{{ URL::to('modpack/modify/recommended?modpack='.$modpack->id) }}&recommended=" + $(this).val(),
		success: function (data) {
			$("#success-ajax").stop(true, true).html(data.success).fadeIn().delay(2000).fadeOut();
		}
	});
});

$("input[name=latest]").change(function() {
	$.ajax({
		type: "GET",
		url: "{{ URL::to('modpack/modify/latest?modpack='.$modpack->id) }}&latest=" + $(this).val(),
		success: function (data) {
			$("#success-ajax").stop(true, true).html(data.success).fadeIn().delay(2000).fadeOut();
		}
	});
});

$(".published").change(function() {
	var checked = 0;
	if (this.checked)
		checked = 1;
	$.ajax({
		type: "GET",
		url: "{{ URL::to('modpack/modify/published') }}?build=" + $(this).attr("rel") + "&published=" + checked,
		success: function (data) {
			$("#success-ajax").stop(true, true).html(data.success).fadeIn().delay(2000).fadeOut();
		}
	})
});

$(".private").change(function() {
	var checked = 0;
	if (this.checked)
		checked = 1;
	$.ajax({
		type: "GET",
		url: "{{ URL::to('modpack/modify/private') }}?build=" + $(this).attr("rel") + "&private=" + checked,
		success: function (data) {
			$("#success-ajax").stop(true, true).html(data.success).fadeIn().delay(2000).fadeOut();
		}
	})
});

$(document).ready(function() {
    $('#dataTables').dataTable();
});

</script>
@endsection