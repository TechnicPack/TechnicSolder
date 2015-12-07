@extends('layouts/master')
@section('title')
    <title>{{ $build->version }} - {{ $build->modpack->name }} - TechnicSolder</title>
@stop
@section('top')
    <script src="{{{ asset('js/selectize.min.js') }}}"></script>
    <link href="{{{ asset('css/selectize.css') }}}" rel="stylesheet">
@endsection
@section('content')
<div class="page-header">
<h1>Build Management</h1>
</div>
<div class="panel panel-default">
	<div class="panel-heading">
	<div class="pull-right">
		<a href="{{ URL::current() }}" class="btn btn-xs btn-warning">Refresh</a>
		<a href="{{ URL::to('modpack/build/' . $build->id . '?action=edit') }}" class="btn btn-xs btn-danger">Edit</a>
	    <a href="{{ URL::to('modpack/view/' . $build->modpack->id) }}" class="btn btn-xs btn-info">Back to Modpack</a>
	</div>
	Build Info: {{ $build->modpack->name }} - Build {{ $build->version }}
	</div>
	<div class="panel-body">
		<div class="col-md-6">
			<label>Build Version: <span class="label label-default">{{ $build->version }}</span></label><br>
			<label>Minecraft Version: <span class="label label-default">{{ $build->minecraft }}</span></label><br>
		</div>
		<div class="col-md-6">
			<label>Java Version: <span class="label label-default">{{ !empty($build->min_java) ? $build->min_java : 'Not Required'  }}</span></label><br>
			<label>Memory (<i>in MB</i>): <span class="label label-default">{{ $build->min_memory != 0 ? $build->min_memory : 'Not Required' }}</span></label>
		</div>
	</div>
</div>
<div class="panel panel-default">
	<div class="panel-heading">
	Build Management: {{ $build->modpack->name }} - Build {{ $build->version }}
	</div>
	<div class="panel-body">
		<div class="table-responsive">
		<table class="table">
			<thead>
				<th style="width: 60%">Add a Mod</th>
				<th></th>
				<th></th>
			</thead>
			<tbody>
			<form method="post" action="{{ URL::to('modpack/build/modify') }}" class="mod-add">
			<input type="hidden" name="build" value="{{ $build->id }}">
			<input type="hidden" name="action" value="add">
			<tr id="mod-list-add">
				<td>
					<i class="icon-plus"></i>
					<select class="form-control" name="mod-name" id="mod" placeholder="Select a Mod...">
						@foreach (Mod::all() as $mod)
						<option value="{{ $mod->name }}">{{ $mod->pretty_name }}</option>
						@endforeach
					</select>
				</td>
				<td>
					<select class="form-control" name="mod-version" id="mod-version" placeholder="Select a Modversion...">
					</select>
				</td>
				<td>
					<button type="submit" class="btn btn-success btn-small">Add Mod</button>
				</td>
			</tr>
			</form>
			</tbody>
		</table>
		</div>
	</div>
</div>
<div class="panel panel-default">
	<div class="panel-heading">
	Build Management: {{ $build->modpack->name }} - Build {{ $build->version }}
	</div>
	<div class="panel-body">
		<div class="table-responsive">
		<table class="table" id="mod-list">
			<thead>
				<th id="mod-header" style="width: 60%">Mod Name</th>
				<th>Version</th>
				<th></th>
			</thead>
			<tbody>
				@foreach ($build->modversions->sortByDesc('build_id', SORT_NATURAL) as $ver)
				<tr>
					<td>{{ HTML::link('mod/view/'.$ver->mod->id, $ver->mod->pretty_name) }} ({{ $ver->mod->name }})</td>
					<td>
						<form method="post" action="{{ URL::to('modpack/build/modify') }}" style="margin-bottom: 0" class="mod-version">
							<input type="hidden" class="build-id" name="build_id" value="{{ $build->id }}">
							<input type="hidden" class="modversion-id" name="modversion_id" value="{{ $ver->pivot->modversion_id }}">
							<input type="hidden" name="action" value="version">
							<div class="form-group input-group">
								<select class="form-control" name="version">
									@foreach ($ver->mod->versions as $version)
									<option value="{{ $version->id }}"{{ $selected = ($ver->version == $version->version ? 'selected' : '') }}>{{ $version->version }}</option>
									@endforeach
								</select>
								<span class="input-group-btn">
									<button type="submit" class="btn btn-primary">Change</button>
								</span>
							</div>
						</form>
					</td>
					<td>
						<form method="post" action="{{ URL::to('modpack/build/modify') }}" style="margin-bottom: 0" class="mod-delete">
							<input type="hidden" name="build_id" value="{{ $build->id }}">
							<input type="hidden" class="modversion-id" name="modversion_id" value="{{ $ver->pivot->modversion_id }}">
							<input type="hidden" name="action" value="delete">
							<button type="submit" class="btn btn-danger btn-small">Remove</button>
						</form>
					</td>
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
var $select = $("#mod").selectize({
			dropdownParent: "body",
			persist: false,
			maxItems: 1,
			sortField: {
				field: 'text',
				direction: 'asc'
			},
		});
var mod = $select[0].selectize;
var $select = $("#mod-version").selectize({
			dropdownParent: "body",
			persist: false,
			maxItems: 1,
			sortField: {
					field: 'text',
					direction: 'asc'
				},
		});
var modversion = $select[0].selectize;

$(".mod-version").submit(function(e) {
	e.preventDefault();
	$.ajax({
		type: "POST",
		url: "{{ URL::to('modpack/modify/version') }}",
		data: $(this).serialize(),
		success: function (data) {
			console.log(data.reason);
			if(data.status == 'success'){
				$.jGrowl("Modversion Updated", { group: 'alert-success' });
			} else if(data.status == 'failed') {
				$.jGrowl("Unable to update modversion", { group: 'alert-warning' });
			} else if(data.status == 'aborted') {
				$.jGrowl("Mod was already set to that version", { group: 'alert-success' });
			}
		},
		error: function (xhr, textStatus, errorThrown) {
			$.jGrowl(textStatus + ': ' + errorThrown, { group: 'alert-danger' });
		}
	});
});

$(".mod-delete").submit(function(e) {
	e.preventDefault();
	$.ajax({
		type: "POST",
		url: "{{ URL::to('modpack/modify/delete') }}",
		data: $(this).serialize(),
		success: function (data) {
			console.log(data.reason);
			if(data.status == 'success'){
				$.jGrowl("Modversion Deleted", { group: 'alert-success' });
			} else {
				$.jGrowl("Unable to delete modversion", { group: 'alert-warning' });
			}
		},
		error: function (xhr, textStatus, errorThrown) {
			$.jGrowl(textStatus + ': ' + errorThrown, { group: 'alert-danger' });
		}
	});
	$(this).parent().parent().fadeOut();
});

$(".mod-add").submit(function(e) {
	e.preventDefault();
	if($("#mod-version").val()){
		$.ajax({
			type: "POST",
			url: "{{ URL::to('modpack/modify/add') }}",
			data: $(this).serialize(),
			success: function (data) {
				if(data.status == 'success'){
					$("#mod-list-add").after('<tr><td>' + data.pretty_name + '</td><td>' + data.version + '</td><td></td></tr>');
					$.jGrowl("Mod " + data.pretty_name + " added at " + data.version, { group: 'alert-success' });
				} else {
					$.jGrowl("Unable to add mod. Reason: " + data.reason, { group: 'alert-warning' });
				}
			},
			error: function (xhr, textStatus, errorThrown) {
				$.jGrowl(textStatus + ': ' + errorThrown, { group: 'alert-danger' });
			}
		});
	} else {
		$.jGrowl("Please select a Modversion", { group: 'alert-warning'});
	}
});

function refreshModVersions() {
	modversion.disable();
	modversion.clearOptions();
	$.ajax({
		type: "GET",
		url: "{{ URL::to('api/mod/') }}/" + mod.getValue(),
		success: function (data) {
			if (data.versions.length === 0){
				$.jGrowl("No Modversions found for " + data.pretty_name, { group: 'alert-warning' });
				$("#mod-version").attr("placeholder", "No Modversions found...");
			} else {
				$(data.versions).each(function(e, m) {
					modversion.addOption({value: m, text: m});
					modversion.refreshOptions(false);
					$("#mod-version").attr("placeholder", "Select a Modversion...");
				});
			}
		},
		error: function (xhr, textStatus, errorThrown) {
			$.jGrowl(textStatus + ': ' + errorThrown, { group: 'alert-danger' });
		}
	});
	modversion.enable();
}

mod.on('change', refreshModVersions);

$( document ).ready(function() {
	$("#mod-list").dataTable({
    	"order": [[ 0, "asc" ]],
    	"autoWidth": false,
    	"columnDefs": [
			{ "width": "60%", "targets": 0 },
			{ "width": "30%", "targets": 1 }
		]
    });
    refreshModVersions();
});
</script>
@endsection
