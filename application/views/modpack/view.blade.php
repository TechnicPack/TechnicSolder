@layout('layouts/modpack')
@section('content')
<h1>Modpack Management</h1>
<hr>
<h2>{{ $modpack->name }}</h2>
<hr>
<a class="btn btn-primary pull-right" href="{{ URL::to('modpack/addbuild/'.$modpack->id) }}">Create New Build</a>
<div class="alert alert-success" id="success-ajax" style="width: 500px;display: none">
</div>
{{ Table::open() }}
{{ Table::headers('#', 'Build Number', 'Mod Count', 'Rec', 'Latest', 'Created', '') }}
@foreach ($modpack->builds as $build)
	<tr>
		<td>{{ $build->id }}</td>
		<td>{{ $build->version }}</td>
		<td>{{ count($build->modversions) }}</td>
		<td><input type="radio" name="recommended" value="{{ $build->version }}"{{ $checked = ($modpack->recommended == $build->version ? " checked" : "") }}></td>
		<td><input type="radio" name="latest" value="{{ $build->version }}"{{ $checked = ($modpack->latest == $build->version ? " checked" : "") }}></td>
		<td>{{ $build->created_at }}</td>
		<td>{{ HTML::link('modpack/build/'.$build->id, "Manage") }}</td>
	</tr>
@endforeach
{{ Table::close() }}
<script type="text/javascript">

$("input[name=recommended]").change(function() {
	$.ajax({
		type: "GET",
		url: "{{ URL::to('modpack/modify/recommended?modpack='.$modpack->id) }}&recommended=" + $(this).val(),
		success: function (data) {
			$("#success-ajax").html(data.success).fadeIn().delay(2000).fadeOut();
		}
	});
});

$("input[name=latest]").change(function() {
	$.ajax({
		type: "GET",
		url: "{{ URL::to('modpack/modify/latest?modpack='.$modpack->id) }}&latest=" + $(this).val(),
		success: function (data) {
			$("#success-ajax").html(data.success).fadeIn().delay(2000).fadeOut();
		}
	});
});

</script>
@endsection