@layout('layouts/master')
@section('content')
<div class="page-header">
<h1>Modpack Management</h1>
</div>
<div class="panel panel-default">
	<div class="panel-heading">
	Create New Build ({{ $modpack->name }})
	</div>
	<div class="panel-body">
		@if ($errors->all())
			<div class="alert alert-error">
			@foreach ($errors->all() as $error)
				{{ $error }}<br />
			@endforeach
			</div>
		@endif
		{{ Form::open() }}
		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
		            <label for="version">Build Number</label>
		            <input type="text" class="form-control" name="version" id="version">
		        </div>
		        <div class="form-group">
		            <label for="version">Minecraft Version</label>
		            <select class="form-control" name="minecraft">
						@foreach ($minecraft as $version)
						<option value="{{ $version->version }}:{{ $version->md5 }}">{{ $version->version }}</option>
						@endforeach
					</select>
		        </div>
		        <div class="form-group">
		            <label for="clone">Clone Build</label>
		            <select class="form-control" name="clone">
						<option value="">Do not clone</option>
						@foreach ($modpack->builds as $build)
							<option value="{{ $build->id }}">{{ $build->version }}</option>
						@endforeach
					</select>
					<p class="help-block">This will clone all the mods and mod versions of another build in this pack.</p>
		        </div>
			</div>
			<div class="col-md-6">
				<p>All new builds by default will not be available in the API. They need to be published before they will show up.</p>
			</div>
		</div>
		{{ Form::actions(array(Button::primary_submit('Add New Build'))) }}
		{{ Form::close() }}
	</div>
</div>
@endsection