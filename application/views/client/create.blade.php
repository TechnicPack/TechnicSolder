@layout('layouts/master')
@section('content')
<h1>Client Management</h1>
<hr>
<div class="panel panel-default">
	<div class="panel-heading">
	Add Client
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
				{{ Form::hidden("add-client", 1) }}
				<div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" class="form-control" name="name" id="name">
                </div>
                <div class="form-group">
                    <label for="uuid">UUID</label>
                    <input type="text" class="form-control" name="uuid" id="uuid">
                </div>
				{{ Form::actions(array(Button::primary_submit('Add Client'),Button::link(URL::to('client/list'),'Go Back'))) }}
				{{ Form::close() }}
			</div>
		</div>
	</div>
</div>
@endsection