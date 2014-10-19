@layout('layouts/mod')
@section('navigation')
@parent
<li class="nav-header">Mod: {{ $mod->name }}</li>
<li><a href="{{ URL::to('mod/view/'.$mod->id) }}"><i class="icon-align-left"></i> Mod Details</a>
<li class="active"><a href="{{ URL::to('mod/versions/'.$mod->id) }}"><i class="icon-tag"></i> Mod Versions</a></li>
@endsection
@section('content')
<h1>Mod Library</h1>
<hr>
<h2>
	@if (!empty($mod->pretty_name))
		{{ $mod->pretty_name }} Versions <small>{{ $mod->name }}</small>
	@else
		{{ $mod->name }} Versions
	@endif
</h2>
<hr>
<p>Solder currently does not support uploading files directly to it. Your repository still needs to exist and follow a strict directory structure. When you add versions the URL will be verified to make sure the file exists before it is added to Solder. The directory stucture for mods is as follow:</p>
	<blockquote><strong>/mods/[modslug]/[modslug]-[version].zip</strong></blockquote>
{{ Table::open() }}
{{ Table::headers('','Version', 'MD5', 'Download URL', '') }}
<tr id="add-row">
	<form method="post" id="add" action="{{ URL::to('mod/addversion') }}">
		<input type="hidden" name="mod-id" value="{{ $mod->id }}">
		<td></td>
		<td>
			<input type="text" name="add-version" id="add-version" class="input-small"></td>
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
		<td class="url" rel="{{ $ver->id }}"><a href="{{ Config::get('solder.mirror_url').'mods/'.$mod->name.'/'.$mod->name.'-'.$ver->version.'.zip' }}">{{ Config::get('solder.mirror_url').'mods/'.$mod->name.'/'.$mod->name.'-'.$ver->version.'.zip' }}</a></td>
		<td>{{ HTML::link('mod/rehash/'.$ver->id,'Rehash', 'class="btn btn-primary btn-small rehash" rel="'.$ver->id.'"') }} {{ HTML::link('mod/deleteversion/'.$ver->id,'Delete', 'class="btn btn-danger btn-small delete" rel="'.$ver->id.'"') }}</td>
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
{{ Table::close() }}
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
					$("#add-row").after('<tr><td></td><td>' + data.version + '</td><td>' + data.md5 + '</td><td><a href="{{ Config::get("solder.mirror_url") }}mods/{{ $mod->name }}/{{ $mod->name }}-' + data.version + '.zip" target="_blank">{{ Config::get("solder.mirror_url") }}mods/{{ $mod->name }}/{{ $mod->name }}-' + data.version + '.zip</a></td><td>Refresh</td></tr>');
				} else {
					alert(data.reason);
				}
			}
		})
	}
});

$('.version-icon').click(function() {
	$('.version-details[rel=' + $(this).attr('rel') + "]").toggle(function() {
		$('.version-icon[rel=' + $(this).attr('rel') + "]").toggleClass("icon-minus");
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

</script>
@endsection