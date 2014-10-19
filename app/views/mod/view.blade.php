@layout('layouts/master')
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
            <li class="active"><a href="#details" data-toggle="tab">Details</a>
            </li>
            <li><a href="#versions" data-toggle="tab">Versions</a>
            </li>
        </ul>
        <div class="tab-content">
	        <div class="tab-pane fade in active" id="details">
	        	<h4>Mod Details</h4>
				@if ($errors->all())
					<div class="alert alert-error">
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
				<form method="post" action="{{ URL::to('mod/view/'.$mod->id) }}">
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
						</div>
					</div>
					{{ Form::actions(array(Button::primary_submit('Save changes'),Button::danger_link('mod/delete/'.$mod->id,'Delete Mod'))) }}
				</form>
			</div>
			<div class="tab-pane fade" id="versions">
                <h4>Mod Versions</h4>
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
				<tr id="add-row">
					<form method="post" id="add" action="{{ URL::to('mod/addversion') }}">
						<input type="hidden" name="mod-id" value="{{ $mod->id }}">
						<td></td>
						<td>
							<input type="text" name="add-version" id="add-version" class="form-control"></td>
						<td>N/A</td>
						<td><span id="add-url">N/A</span></td>
						<td><button type="submit" class="btn btn-success btn-small add">Add Version</button></td>
					</form>
				</tr>
				@foreach ($mod->versions()->order_by('id', 'desc')->get() as $ver)
					<tr class="version" rel="{{ $ver->id }}">
						<td><i class="version-icon fa fa-plus" rel="{{ $ver->id }}"></i></td>
						<td class="version" rel="{{ $ver->id }}">{{ $ver->version }}</td>
						<td><span class="md5" rel="{{ $ver->id }}">{{ $ver->md5 }}</span></td>
						<td class="url" rel="{{ $ver->id }}"><small><a href="{{ Config::get('solder.mirror_url').'mods/'.$mod->name.'/'.$mod->name.'-'.$ver->version.'.zip' }}">{{ Config::get('solder.mirror_url').'mods/'.$mod->name.'/'.$mod->name.'-'.$ver->version.'.zip' }}</a></small></td>
						<td>{{ HTML::link('mod/rehash/'.$ver->id,'Rehash', 'class="btn btn-primary btn-xs rehash" rel="'.$ver->id.'"') }} {{ HTML::link('mod/deleteversion/'.$ver->id,'Delete', 'class="btn btn-danger btn-xs delete" rel="'.$ver->id.'"') }}</td>
					</tr>
					<tr class="version-details" rel="{{ $ver->id }}" style="display: none">
						<td colspan="5">
							<h4>Builds Used In</h4>
							<ul>
							@foreach ($ver->builds as $build)
								<li>{{ HTML::link('modpack/view/'.$build->modpack->id,$build->modpack->name) }} - {{ HTML::link('modpack/build/'.$build->id,$build->version) }}</li>
							@endforeach
							</ul>
						</td>
					</tr>
				@endforeach
				</table>
            </div>
		</div>
	</div>
</div>
@endsection
@section('bottom')
<script type="text/javascript">

$('#add-version').keyup(function() {
	$("#add-url").html('<a href="{{ Config::get("solder.mirror_url") }}mods/{{ $mod->name }}/{{ $mod->name }}-' + $(this).val() + '.zip" target="_blank">{{ Config::get("solder.mirror_url") }}mods/{{ $mod->name }}/{{ $mod->name }}-' + $(this).val() + '.zip</a>');
});

$('#add').submit(function(e) {
	e.preventDefault();

	if ($('#add-version').val() != "") {
		$.ajax({
			type: "POST",
			url: "{{ URL::to('mod/addversion/') }}",
			data: $("#add").serialize(),
			success: function (data) {
				if (data.status == "success") {
					$("#add-row").after('<tr><td></td><td>' + data.version + '</td><td>' + data.md5 + '</td><td><a href="{{ Config::get("solder.mirror_url") }}mods/{{ $mod->name }}/{{ $mod->name }}-' + data.version + '.zip" target="_blank">{{ Config::get("solder.mirror_url") }}mods/{{ $mod->name }}/{{ $mod->name }}-' + data.version + '.zip</a></td><td></td></tr>');
				} else {
					$("#danger-ajax").stop(true, true).html(data.reason).fadeIn().delay(2000).fadeOut();
				}
			}
		})
	}
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
		url: "{{ URL::to('mod/rehash/') }}" + $(this).attr('rel'),
		success: function (data) {
			$(".md5[rel=" + data.version_id + "]").html(data.md5);
			$(".md5[rel=" + data.version_id + "]").fadeIn();
		}
	});
});

$('.delete').click(function(e) {
	e.preventDefault();
	$.ajax({
		type: "GET",
		url: "{{ URL::to('mod/deleteversion/') }}" + $(this).attr('rel'),
		success: function (data) {
			$('.version[rel=' + data.version_id + ']').fadeOut();
			$('.version-details[rel=' + data.version_id + ']').fadeOut();
		}
	});
});

$(document).ready(function() {
	var tab = window.location.hash.substr(1);

	if (tab == "versions") {
		$('#tabs a[href="#versions"]').tab('show');
	} else {
		$('#tabs a[href="#details"]').tab('show');
	}

	$('#tabs a[href="#versions"]').click(function() {
		window.location.hash = "#versions";
	});

	$('#tabs a[href="#details"]').click(function() {
		window.location.hash = "#details";
	});
});

</script>
@endsection