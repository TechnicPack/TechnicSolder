@extends('layouts/master')
@section('title')
    <title>{{ empty($mod->pretty_name) ? $mod->name : $mod->pretty_name }} - TechnicSolder</title>
@stop
@section('content')
<div class="page-header">
<h1>Mod Library</h1>
</div>
<div class="panel panel-default">
	<div class="panel-heading">
	@if (!empty($mod->pretty_name))
		{{ $mod->pretty_name }} <small>{{ $mod->name }}</small>
	@else
		{{ $mod->name }}
	@endif
	</div>
	<div class="panel-body">
		<ul class="nav nav-tabs" id="tabs">
            <li class="active"><a href="#details" data-toggle="tab">Details</a></li>
            <li><a href="#versions" data-toggle="tab">Versions</a></li>
        </ul>
        <div class="tab-content">
	        <div class="tab-pane fade in active" id="details">
				<br>
				@if ($errors->all())
					<div class="alert alert-danger">
					@foreach ($errors->all() as $error)
						{{ $error }}<br />
					@endforeach
					</div>
				@endif
				@if (Session::has('success'))
					<div class="alert alert-success">
						{{ Session::get('success') }}
					</div>
				@endif
				<form method="post" action="{{ URL::to('mod/modify/'.$mod->id) }}">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
			                    <label for="pretty_name">Mod Name</label>
			                    <input type="text" class="form-control" name="pretty_name" id="pretty_name" value="{{ $mod->pretty_name }}">
			                </div>
			                <div class="form-group">
			                    <label for="name">Mod Slug</label>
			                    <input type="text" class="form-control" name="name" id="name" value="{{ $mod->name }}">
			                </div>
			                <div class="form-group">
			                    <label for="author">Author</label>
			                    <input type="text" class="form-control" name="author" id="author" value="{{ $mod->author }}">
			                </div>
			                <div class="form-group">
			                    <label for="description">Description</label>
			                    <textarea name="description" id="description" class="form-control" rows="5">{{ $mod->description }}</textarea>
			                </div>
			                <div class="form-group">
			                    <label for="link">Mod Website</label>
			                    <input type="text" class="form-control" name="link" id="link" value="{{ $mod->link }}">
			                </div>
			                <div class="form-group">
			                    <label for="donatelink">Author Donation Link</label>
			                    <input type="text" class="form-control" name="donatelink" id="donatelink" value="{{ $mod->donatelink }}">
			                    <span class="help-block">This is only in use by the official Technic Solder</span>
			                </div>
						</div>
					</div>
					{{ Form::submit('Save Changes', array('class' => 'btn btn-success')) }}
					{{ HTML::link('mod/delete/'.$mod->id, 'Delete Mod', array('class' => 'btn btn-danger')) }}
					{{ HTML::link('mod/list/', 'Go Back', array('class' => 'btn btn-primary')) }}
				</form>
			</div>
			<div class="tab-pane fade" id="versions">
				<br>
				<p>Solder currently does not support uploading files directly to it. Your repository still needs to exist and follow a strict directory structure. When you add versions the URL will be verified to make sure the file exists before it is added to Solder. The directory stucture for mods is as follow:</p>
					<blockquote><strong>/mods/[modslug]/[modslug]-[version].zip</strong></blockquote>
				<div class="alert alert-success" id="success-ajax" style="width: 100%;display: none"></div>
				<div class="alert alert-danger" id="danger-ajax" style="width: 100%;display: none"></div>
				<table class="table">
					<thead>
						<th></th>
						<th style="width: 15%">Version</th>
						<th>MD5</th>
						<th>Download URL</th>
						<th style="width: 15%"></th>
					</thead>
					<tbody>
						<tr id="add-row">
							<form method="post" id="add" action="{{ URL::to('mod/add-version') }}">
								<input type="hidden" name="mod-id" value="{{ $mod->id }}">
								<td></td>
								<td>
									<select type="text" name="add-version" id="add-version" class="form-control" /></td>
								<td>N/A</td>
								<td><span id="add-url">N/A</span></td>
								<td><button type="submit" class="btn btn-success btn-small add">Add Version</button>
								<button id="refresh" class="btn btn-primary btn-small"><i id="refresh-icon" class="fa fa-refresh"></i></button></td>
							</form>
						</tr>
						@foreach ($mod->versions()->orderBy('id', 'desc')->get() as $ver)
						<tr class="version" rel="{{ $ver->id }}">
							<td><i class="version-icon fa fa-plus" rel="{{ $ver->id }}"></i></td>
							<td class="version" rel="{{ $ver->id }}">{{ $ver->version }}</td>
							<td><span class="md5" rel="{{ $ver->id }}">{{ $ver->md5 }}</span></td>
							<td class="url" rel="{{ $ver->id }}"><small><a href="{{ Config::get('solder.mirror_url').'mods/'.$mod->name.'/'.$mod->name.'-'.$ver->version.'.zip' }}">{{ Config::get('solder.mirror_url').'mods/'.$mod->name.'/'.$mod->name.'-'.$ver->version.'.zip' }}</a></small></td>
							<td><button class="btn btn-primary btn-xs rehash" rel="{{ $ver->id }}">Rehash</button> <button class="btn btn-danger btn-xs delete" rel="{{ $ver->id }}">Delete</button>
						</tr>
						<tr class="version-details" rel="{{ $ver->id }}" style="display: none">
							<td colspan="5">
								<h5>Builds Used In</h5>
								<ul>
								@foreach ($ver->builds as $build)
									<li>{{ HTML::link('modpack/view/'.$build->modpack->id,$build->modpack->name) }} - {{ HTML::link('modpack/build/'.$build->id,$build->version) }}</li>
								@endforeach
								</ul>
							</td>
						</tr>
						@endforeach
					</tbody>
				</table>
            </div>
		</div>
	</div>
</div>
@endsection
@section('bottom')
<script type="text/javascript">
function refresh() {
	$('#refresh-icon').addClass("fa-spin");
	$.ajax({
			type: "GET",
			url: "{{ URL::to('mod/file-refresh/'.$mod->id) }}",
			success: function (data) {
				$('#refresh-icon').removeClass("fa-spin");
				if (data.status == "success") {
					$('#add-version').empty();
					if(data.versions.length > 0) {
						$('#add-version').append('<option value=\"\">Select Version</option>');
						$.each(data.versions,function(key, value)
						{
						    $('#add-version').append('<option value=' + value + '>' + value + '</option>');
						    if(data.versions.length == key -1)
						    	$("#add-url").html('<a href="{{ Config::get("solder.mirror_url") }}mods/{{ $mod->name }}/{{ $mod->name }}-' + $(this).val() + '.zip" target="_blank">{{ Config::get("solder.mirror_url") }}mods/{{ $mod->name }}/{{ $mod->name }}-' + $(this).val() + '.zip</a>');
						});
					} else {
						$('#add-version').append('<option value=\"\">N/A</option>');
						$("#add-url").html('N/A');
					}
				} else {
					$("#danger-ajax").stop(true, true).html('Error: ' + data.reason).fadeIn().delay(3000).fadeOut();
				}
			},
			error: function (xhr, textStatus, errorThrown) {
				$('#refresh-icon').removeClass("fa-spin");
				$("#danger-ajax").stop(true, true).html(textStatus + ': ' + errorThrown).fadeIn().delay(3000).fadeOut();
			}
		});
}

$('#add-version').change(function() {
	if ($('#add-version').val() != "") {
		$("#add-url").html('<a href="{{ Config::get("solder.mirror_url") }}mods/{{ $mod->name }}/{{ $mod->name }}-' + $(this).val() + '.zip" target="_blank">{{ Config::get("solder.mirror_url") }}mods/{{ $mod->name }}/{{ $mod->name }}-' + $(this).val() + '.zip</a>');
	} else {
		$("#add-url").html('N/A');
	}
});

$('#add').submit(function(e) {
	e.preventDefault();

	if ($('#add-version').val() != "") {
		$.ajax({
			type: "POST",
			url: "{{ URL::to('mod/add-version/') }}",
			data: $("#add").serialize(),
			success: function (data) {
				if (data.status == "success") {
					refresh();
					$("#add-row").after('<tr><td></td><td>' + data.version + '</td><td>' + data.md5 + '</td><td><a href="{{ Config::get("solder.mirror_url") }}mods/{{ $mod->name }}/{{ $mod->name }}-' + data.version + '.zip" target="_blank">{{ Config::get("solder.mirror_url") }}mods/{{ $mod->name }}/{{ $mod->name }}-' + data.version + '.zip</a></td><td></td></tr>');
					$("#success-ajax").stop(true, true).html('Added mod version at ' + data.version).fadeIn().delay(3000).fadeOut();
				} else {
					$("#danger-ajax").stop(true, true).html('Error: ' + data.reason).fadeIn().delay(3000).fadeOut();
				}
			},
			error: function (xhr, textStatus, errorThrown) {
				$("#danger-ajax").stop(true, true).html(textStatus + ': ' + errorThrown).fadeIn().delay(3000).fadeOut();
			}
		});
	}
});

$('#refresh').click(function(e) {
	e.preventDefault();
	refresh();
});

$('.version-icon').click(function() {
	$('.version-details[rel=' + $(this).attr('rel') + "]").toggle(function() {
		$('.version-icon[rel=' + $(this).attr('rel') + "]").toggleClass("fa-minus");
	});
});

$('.rehash').click(function(e) {
	e.preventDefault();
	$(".md5[rel=" + $(this).attr('rel') + "]").fadeOut();
	$.ajax({
		type: "GET",
		url: "{{ URL::to('mod/rehash/') }}/" + $(this).attr('rel'),
		success: function (data) {
			if (data.status == "success") {
				$(".md5[rel=" + data.version_id + "]").html(data.md5);
				$("#success-ajax").stop(true, true).html('MD5 hashing complete.').fadeIn().delay(3000).fadeOut();
				$(".md5[rel=" + data.version_id + "]").fadeIn();
			} else {
				$("#danger-ajax").stop(true, true).html('Error: ' + data.reason).fadeIn().delay(3000).fadeOut();
			}
		},
		error: function (xhr, textStatus, errorThrown) {
			$("#danger-ajax").stop(true, true).html(textStatus + ': ' + errorThrown).fadeIn().delay(3000).fadeOut();
		}
	});
});

$('.delete').click(function(e) {
	e.preventDefault();
	$.ajax({
		type: "GET",
		url: "{{ URL::to('mod/delete-version/') }}/" + $(this).attr('rel'),
		success: function (data) {
			if (data.status == "success") {
				refresh();
				$('.version[rel=' + data.version_id + ']').fadeOut();
				$('.version-details[rel=' + data.version_id + ']').fadeOut();
				$("#success-ajax").stop(true, true).html('Mod version ' + data.version + ' deleted.').fadeIn().delay(3000).fadeOut();
			} else {
				$("#danger-ajax").stop(true, true).html('Error: ' + data.reason).fadeIn().delay(3000).fadeOut();
			}
		},
		error: function (xhr, textStatus, errorThrown) {
			$("#danger-ajax").stop(true, true).html(textStatus + ': ' + errorThrown).fadeIn().delay(3000).fadeOut();
		}
	});
});

$(document).ready(function() {
	refresh();
	var tab = window.location.hash.substr(1);

	if (tab == "versions") {
		$('#tabs a[href="#versions"]').tab('show');
	} else {
		$('#tabs a[href="#details"]').tab('show');
	}

	/* Disabled for now, there is ample screen space that all we need to do is switch the tabs
	        changing location is disorienting.

	$('#tabs a[href="#versions"]').click(function() {
		window.location.hash = "#versions";
	});

	$('#tabs a[href="#details"]').click(function() {
		window.location.hash = "#details";
	});*/
});

</script>
@endsection
