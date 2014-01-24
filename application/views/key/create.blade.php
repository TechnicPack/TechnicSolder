@layout('layouts/master')
@section('content')
<h1>API Key Management</h1>
<hr>
<div class="panel panel-default">
	<div class="panel-heading">
	Add API Key
	</div>
	<div class="panel-body">
		@if ($errors->all())
		    <div class="alert alert-error">
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
				{{ Form::actions(array(Button::primary_submit('Add API Key'),Button::link(URL::to('key/list'),'Go Back'))) }}
				{{ Form::close() }}
			</div>
		</div>
	</div>
</div>
@endsection