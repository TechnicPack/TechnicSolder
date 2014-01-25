@layout('layouts/master')
@section('content')
<div class="page-header">
<h1>Modpack Management</h1>
</div>
<div class="panel panel-default">
	<div class="panel-heading">
	Delete Modpack: {{ $modpack->name }}
	</div>
	<div class="panel-body">
		<p>Deleting a modpack is irreversible. All associated builds will be immediately removed. This will remove them from your API. Users with this modpack already on their launcher will be able to continue to use it in "Offline Mode."</p>
		{{ Form::open() }}
		<button type="submit" class="btn btn-danger">Confirm Deletion</button> 
			<a href="{{ Request::referrer() }}" class="btn">Go Back</a>
		{{ Form::close() }}
	</div>
</div>
@endsection