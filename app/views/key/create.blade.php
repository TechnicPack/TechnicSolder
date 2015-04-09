@extends('layouts/master')
@section('title')
    <title>Create API Key - TechnicSolder</title>
@stop
@section('content')
<h1>API Key Management</h1>
<hr>
<div class="panel panel-default">
	<div class="panel-heading">
	Add API Key
	</div>
	<div class="panel-body">
		@if ($errors->all())
		    <div class="alert alert-danger">
		    @foreach ($errors->all() as $error)
		        {{ $error }}<br />
		    @endforeach
		    </div>
		@endif
		<div class="row">
			<div class="col-md-6">
				{{ Form::open() }}
				{{ Form::hidden("add-key", 1) }}
				<div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" class="form-control" name="name" id="name">
                </div>
                <div class="form-group">
                    <label for="api_key">API Key</label>
                    <input type="text" class="form-control" name="api_key" id="api_key">
                </div>
				{{ Form::submit('Add Key', array('class' => 'btn btn-success')) }}
				{{ HTML::link('key/list/', 'Go Back', array('class' => 'btn btn-primary')) }}
				{{ Form::close() }}
			</div>
		</div>
	</div>
</div>
@endsection