@layout('layouts/modpack')
@section('content')
<h1>Modpack Management</h1>
<hr>
<h2>Delete request for build {{ $build->version }} ({{ $build->modpack->name }})</h2>
<hr>
<p>Are you sure you want to delete this build? This action is irreversible!</p>
<form method="post" action="{{ URL::full() }}">
	<input type="hidden" name="confirm-delete" value="1">
	{{ Form::actions(array(Button::danger_submit('Delete Build'),Button::link('modpack/view/'.$build->modpack->id,'Go Back'))) }}
</form>
@endsection