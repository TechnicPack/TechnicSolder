@layout('layouts/mod')
@section('navigation')
@parent
<li class="nav-header">Mod: {{ $mod->name }}</li>
<li class="active"><a href="{{ URL::to('mod/view/'.$mod->id) }}"><i class="icon-align-left"></i> Mod Details</a>
<li><a href="{{ URL::to('mod/versions/'.$mod->id) }}"><i class="icon-tag"></i> Mod Versions</a></li>
@endsection
@section('content')
<h1>Mod Library</h1>
<hr>
<h2>
	@if (!empty($mod->pretty_name))
		{{ $mod->pretty_name }} <small>{{ $mod->name }}</small>
	@else
		{{ $mod->name }}
	@endif
</h2>
<hr>
@if ($errors->all())
	<div class="alert alert-error">
	@foreach ($errors->all() as $error)
		{{ $error }}<br />
	@endforeach
	</div>
@endif
@if (Session::has('success'))
	<div class="alert alert-success">
		{{ Session::get('success') }}
	</div>
@endif
<form method="post" action="{{ URL::to('mod/view/'.$mod->id) }}">
	<label for="pretty_name">Mod Name</label>
	<input type="text" name="pretty_name" id="pretty_name" class="input-xxlarge" value="{{ $mod->pretty_name }}">
	<label for="name">Mod Slug</label>
	<input type="text" name="name" id="name" class="input-xxlarge" value="{{ $mod->name }}">
	<label for="author">Author</label>
	<input type="text" name="author" id="author" class="input-large" value="{{ $mod->author }}">
	<label for="description">Description</label>
	<textarea name="description" id="description" class="input-xxlarge" rows="5">{{ $mod->description }}</textarea>
	<label for="link">Mod Website</label>
	<input type="text" name="link" id="link" class="input-xxlarge" value="{{ $mod->link }}">
	{{ Form::actions(array(Button::primary_submit('Save changes'),Button::danger_link('mod/delete/'.$mod->id,'Delete Mod'))) }}
</form>
@endsection