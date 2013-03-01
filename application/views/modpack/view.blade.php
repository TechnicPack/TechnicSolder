@layout('layouts/modpack')
@section('content')
<h1>Modpack Management</h1>
<hr>
<h2>{{ $modpack->name }}</h2>
<hr>
<a class="btn btn-primary pull-right" href="{{ URL::to('modpack/addbuild/'.$modpack->id) }}">Create New Build</a>
<div class="alert alert-success" id="success-ajax" style="width: 500px;display: none">
</div>
@if (Session::has('deleted'))
<div class="alert alert-error">
	{{ Session::get('deleted') }}
</div>
@endif
{{ Table::open() }}
{{ Table::headers('#', 'Build Number', 'Mod Count', 'Rec', 'Latest', 'Published', 'Created', '') }}
@foreach ($modpack->builds as $build)
	<tr>
		<td>{{ $build->id }}</td>
		<td>{{ $build->version }}</td>
		<td>{{ count($build->modversions) }}</td>
		<td><input type="radio" name="recommended" value="{{ $build->version }}"{{ $checked = ($modpack->recommended == $build->version ? " checked" : "") }}></td>
		<td><input type="radio" name="latest" value="{{ $build->version }}"{{ $checked = ($modpack->latest == $build->version ? " checked" : "") }}></td>
		<td><input type="checkbox" name="published" value="1" class="published" rel="{{ $build->id }}"{{ $checked = ($build->is_published ? " checked" : "") }}></td>
		<td>{{ $build->created_at }}</td>
		<td>{{ HTML::link('modpack/build/'.$build->id, "Manage",'class="btn btn-small btn-primary"') }} {{ HTML::link('modpack/build/'.$build->id.'?action=delete', "Delete",'class="btn btn-small btn-danger"') }}</td>
	</tr>
@endforeach
{{ Table::close() }}
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

</script>
@endsection