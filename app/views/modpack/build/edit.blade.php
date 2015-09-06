@extends('layouts/master')
@section('title')
    <title>{{ $build->version }} - {{ $build->modpack->name }} - TechnicSolder</title>
@stop
@section('content')
<div class="page-header">
<h1>Build Management</h1>
</div>
<div class="panel panel-default">
	<div class="panel-heading">
	Edit Build ({{ $build->version }})
	</div>
	<div class="panel-body">
		@if ($errors->all())
			<div class="alert alert-danger">
			@foreach ($errors->all() as $error)
				{{ $error }}<br />
			@endforeach
			</div>
		@endif
		{{ Form::open(array('url' => URL::full() )) }}
		<input type="hidden" name="confirm-edit" value="1">
		<div class="row">
			<div class="col-md-6">
				<h4>Edit Build</h4>
				<p>Here you can modify the properties of existing builds.</p>
				<div class="alert alert-warning">If changes are made, users will need to re-install the modpack if they have already installed this build.</div>
				<hr>
				<div class="form-group">
		            <label for="version">Build Number</label>
		            <input type="text" class="form-control" name="version" id="version"  value="{{ $build->version }}">
		        </div>
		        <div class="form-group">
		            <label for="version">Minecraft Version</label>
		            <select class="form-control" name="minecraft">
						@foreach ($minecraft as $version)
						<option value="{{ $version['version'] }}" {{ ($build->minecraft == $version['version'] ? ' selected' : '') }}>{{ $version['version'] }}</option>
						@endforeach
					</select>
		        </div>
			</div>
			<div class="col-md-6">
				<h4>Build Requirements</h4>
				<p>These are requirements that are passed onto the launcher to prevent players from playing your pack without the required minumum settings</p>
				<hr>
				<div class="form-group">
		            <label for="java-version">Minimum Java Version</label>
		            <select class="form-control" name="java-version" id="java-version">
						<option value="1.8"{{ ($build->min_java == '1.8' ? ' selected' : '') }}>Java 1.8</option>
						<option value="1.7"{{ ($build->min_java == '1.7' ? ' selected' : '') }}>Java 1.7</option>
						<option value="1.6"{{ ($build->min_java == '1.6' ? ' selected' : '') }}>Java 1.6</option>
						<option value=""{{ ($build->min_java == '' ? ' selected' : '') }}>No Requirement</option>
					</select>
		        </div>
		        <div class="form-group">
		            <label for="memory">Minimum Memory (<i>in MB</i>)</label>
		            <div class="input-group">
						<span class="input-group-addon">
							<input type="checkbox" id="memory-enabled" name="memory-enabled" aria-label="mb"{{ $build->min_memory ? ' checked' : '' }}>
						</span>
		            	<input type="text" class="form-control" name="memory" id="memory" aria-label="mb" aria-describedby="addon-mb" value="{{ $build->min_memory }}" {{ $build->min_memory ? '' : ' disabled' }}>
		            	<span class="input-group-addon" id="addon-mb">MB</span>
					</div>
					<p class="help-block">Check the checkbox to enable the memory requirement.</p>
		        </div>
			</div>
		</div>
		<hr>
		{{ Form::submit('Update Build', array('class' => 'btn btn-success')) }}
		{{ HTML::link('modpack/build/'.$build->id.'?action=delete', 'Delete Build', array('class' => 'btn btn-danger')) }}
		{{ HTML::link('modpack/build/'.$build->id, 'Go Back', array('class' => 'btn btn-primary')) }}
		{{ Form::close() }}
	</div>
</div>
@endsection
@section('bottom')
<script type="text/javascript">
$('#memory-enabled').change(function(){
    if ($('#memory-enabled').is(':checked') == true){
        $('#memory').prop('disabled', false);
    } else {
        $('#memory').val('').prop('disabled', true);
    }
});
</script>
@endsection
