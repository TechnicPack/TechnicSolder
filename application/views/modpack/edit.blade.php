@layout('layouts/master')
@section('content')
<div class="page-header">
<h1>Modpack Management</h1>
</div>
<div class="panel panel-default">
	<div class="panel-heading">
	Editing Modpack: {{ $modpack->name }}
	</div>
	<div class="panel-body">
		@if ($errors->all())
			<div class="alert alert-error">
			@foreach ($errors->all() as $error)
				{{ $error }}<br />
			@endforeach
			</div>
		@endif
		<form method="POST" action="{{ URL::current() }}" accept-charset="UTF-8" enctype="multipart/form-data">
		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
                    <label for="name">Modpack Name</label>
                    <input type="text" class="form-control" name="name" id="name" value="{{ $modpack->name }}">
                </div>
                <div class="form-group">
                    <label for="slug">Modpack Slug</label>
                    <input type="text" class="form-control" name="slug" id="slug" value="{{ $modpack->slug }}">
                </div>
				<div class="form-group">
					<label class="control-label" for="hidden">Hide Modpack</label>
					<div class="controls">
						<input type="checkbox" name="hidden" id="hidden"{{ $checked = ($modpack->hidden ? ' checked' : '') }}>
						<span class="help-block">Hidden modpacks will not show up in the API response for the modpack list regardless of whether or not a client has access to the modpack.</span>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="private">Private Modpack</label>
					<div class="controls">
						<input type="checkbox" name="private" id="private"{{ ($modpack->private ? ' checked' : '') }}>
						<span class="help-block">Private modpacks will only be available to clients that are linked to this modpack. You can link clients below. You can also individually mark builds as private.</span>
					</div>
				</div>
				<h3>Client Access</h3>
				<p>Check the clients below you want to have access to this modpack if anything is set to private.</p>
				@foreach (Client::all() as $client)
				<div style="display: inline-block; padding-right: 10px;"><input type="checkbox" name="clients[]" value="{{ $client->id }}"{{ (in_array($client->id, $clients) ? ' checked' : '') }}> {{ $client->name }}</div>
				@endforeach
			</div>
			<div class="col-md-6">
				<h3>Image Management</h3>
				<p>Upload your modpacks resources here. These images are what will be served to the launcher. If your modpack already has images on your mirror, they will remain working until the first time you upload them here.</p>
				<div class="form-group">
					<label class="control-label" for="logo">Modpack Logo</label>
					<div class="controls">
						@if ($modpack->logo)
						<div class="modpack-logo">
							@if (Config::get('solder.use_s3'))
							<img src="{{ Config::get('solder.s3_url') }}resources/{{ $modpack->slug }}/logo.png?{{ TimeUtils::getTimestampDate($modpack->updated_at) }}">
							@else
							<img src="{{ URL::to_asset('resources/' . $modpack->slug . '/logo.png') }}">
							@endif
						</div>
						@endif
						<input type="file" name="logo" id="logo">
						<span class="help-block">Required Size: 180x110</span>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label" for="background">Modpack Background</label>
					<div class="controls">
						@if ($modpack->background)
						<div class="modpack-background">
							@if (Config::get('solder.use_s3'))
							<img src="{{ Config::get('solder.s3_url') }}resources/{{ $modpack->slug }}/background.png?{{ TimeUtils::getTimestampDate($modpack->updated_at) }}" style="width: 300px;">
							@else
							<img src="{{ URL::to_asset('resources/' . $modpack->slug . '/background.png') }}" style="width: 300px;">
							@endif
						</div>
						@endif
						<input type="file" name="background" id="background">
						<span class="help-block">Required Size: 880x520</span>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="icon">Modpack Icon</label>
					<div class="controls">
						@if ($modpack->icon)
						<div class="modpack-icon">
							@if (Config::get('solder.use_s3'))
							<img src="{{ Config::get('solder.s3_url') }}resources/{{ $modpack->slug }}/icon.png?{{ TimeUtils::getTimestampDate($modpack->updated_at) }}">
							@else
							<img src="{{ URL::to_asset('resources/' . $modpack->slug . '/icon.png') }}">
							@endif
						</div>
						@endif
						<input type="file" name="icon" id="icon">
						<span class="help-block">Recommended Size: 50x50</span>
					</div>
				</div>
			</div>
		</div>
		{{ Form::actions(array(Button::primary_submit('Save Changes'),Button::danger_link(URL::to('modpack/delete/'.$modpack->id),'Delete Modpack'))) }}
		{{ Form::close() }}
	</div>
</div>
<script type="text/javascript">
$("#slug").slugify('#name');
</script>
@endsection