@layout('layouts/modpack')
@section('content')
<h1>Modpack Management</h1>
<hr>
<h2>Create New Build ({{ $modpack->name }})</h2>
<p>All new builds by default will not be available in the API. They need to be published before they will show up.</p>
@if ($errors->all())
	<div class="alert alert-error">
	@foreach ($errors->all() as $error)
		{{ $error }}<br />
	@endforeach
	</div>
@endif
{{ Form::horizontal_open() }}
<div class="control-group">
	<label class="control-label" for="version">Build Number</label>
	<div class="controls">
		<input class="input-large" type="text" name="version" id="version">
	</div>
</div>
<div class="control-group">
	<label class="control-label" for="minecraft">Minecraft Version</label>
	<div class="controls">
		<select name="minecraft">
			<option value="1.4.7">1.4.7</option>
			<option value="1.4.6">1.4.6</option>
			<option value="1.2.5">1.2.5</option>
		</select>
	</div>
</div>
<div class="control-group">
	<label class="control-label" for="slug">Clone Build</label>
	<div class="controls">
		<select name="clone">
			<option value="">Do not clone</option>
			@foreach ($modpack->builds as $build)
				<option value="{{ $build->id }}">{{ $build->version }}</option>
			@endforeach
		</select>
		<p class="help-block">This will clone all the mods and mod versions of another build in this pack.</p>
	</div>
</div>
{{ Form::actions(array(Button::primary_submit('Add New Build'))) }}
{{ Form::close() }}
@endsection