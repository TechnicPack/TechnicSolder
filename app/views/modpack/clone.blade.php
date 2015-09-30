@extends('layouts/master')
@section('title')
	<title>Clone Modpack - TechnicSolder</title>
@stop
@section('top')
	<script src="{{{ asset('js/selectize.min.js') }}}"></script>
	<link href="{{{ asset('css/selectize.css') }}}" rel="stylesheet">
@endsection
@section('content')
	<div class="page-header">
		<h1>Modpack Management</h1>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">
			Clone Modpack
		</div>
		<div class="panel-body">
			@if ($errors->all())
				<div class="alert alert-danger">
					@foreach ($errors->all() as $error)
						{{ $error }}<br />
					@endforeach
				</div>
			@endif
			{{ Form::open() }}
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<label for="source">Source Modpack</label>
						<select class="form-control" name="source" id="source" placeholder="Select a modpack...">
							<option value="">Select a modpack...</option>
							@foreach($modpacks as $modpack)
							<option value="{{ $modpack->slug }}">
								{{ $modpack->name }}
							</option>
							@endforeach
						</select>
					</div>
					<div class="form-group">
						<label for="destination">Destination Modpack</label>
						<select class="form-control" name="destination" id="destination" placeholder="Select a modpack...">
							<option value="">Select a modpack...</option>
							@foreach($modpacks as $modpack)
								<option value="{{ $modpack->slug }}">
									{{ $modpack->name }}
								</option>
							@endforeach
						</select>
					</div>
				</div>
				<div class="col-md-6">
					<p>Cloning a modpack will duplicate all of a modpack's builds and history into another modpack.</p>
					<p class="alert alert-danger">If the destination modpack already exists all of its builds will be destroyed and replaced with copies of the source modpack's builds, only do this if you're certain you know what you're doing. Otherwise, create a new modpack by typing its name.</p>
					<p>If you wish to link this modpack with an existing Technic Platform modpack, the slug must be identical to your slug on the Platform!</p>
				</div>
			</div>
			{{ Form::submit('Clone Modpack', array('class' => 'btn btn-success')) }}
			{{ HTML::link('modpack/list/', 'Go Back', array('class' => 'btn btn-primary')) }}
			{{ Form::close() }}
		</div>
	</div>
	<script type="text/javascript">
		var $select = $("#source").selectize({
			persist: false,
			maxItems: 1,
			sortField: {
				field: 'text',
				direction: 'asc'
			}
		});
		var source = $select[0].selectize;

		var $select = $("#destination").selectize({
			persist: false,
			maxItems: 1,
			create: true,
			sortField: {
				field: 'text',
				direction: 'asc'
			}
		});
		var destination = $select[0].selectize;
	</script>
@endsection