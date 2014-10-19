@layout('layouts/master')
@section('content')
<h1>Modpack Management</h1>
<hr>
<div class="panel panel-default">
	<div class="panel-heading">
	Delete request for build {{ $build->version }} ({{ $build->modpack->name }})
	</div>
	<div class="panel-body">
		<p>Are you sure you want to delete this build? This action is irreversible!</p>
		<form method="post" action="{{ URL::full() }}">
			<input type="hidden" name="confirm-delete" value="1">
			{{ Form::actions(array(Button::danger_submit('Delete Build'),Button::link('modpack/view/'.$build->modpack->id,'Go Back'))) }}
		</form>
	</div>
</div>
@endsection