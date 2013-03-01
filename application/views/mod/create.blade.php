@layout('layouts/mod')
@section('content')
<h1>Mod Library</h1>
<hr>
<h2>Add a new mod</h2>
<p>Because Solder doesn't do any file handling yet you will need to manually manage your set of mods in your repository. The mod repository structure is very strict and must match your Solder data exact. An example of your mod directory structure will be listed below:</p>
<blockquote>/mods/<span class="modslug">[modslug]</span>/<br>
	/mods/<span class="modslug">[modslug]</span>/<span class="modslug">[modslug]</span>-[version].zip
</blockquote>
@if ($errors->all())
	<div class="alert alert-error">
	@foreach ($errors->all() as $error)
		{{ $error }}<br />
	@endforeach
	</div>
@endif
<form method="post" action="{{ URL::to('mod/create') }}">
	<label for="pretty_name">Mod Name</label>
	<input type="text" name="pretty_name" id="pretty_name" class="input-xxlarge">
	<label for="name">Mod Slug</label>
	<input type="text" name="name" id="name" class="input-xxlarge">
	<label for="author">Author</label>
	<input type="text" name="author" id="author" class="input-large">
	<label for="description">Description</label>
	<textarea name="description" id="description" class="input-xxlarge" rows="5"></textarea>
	<label for="link">Mod Website</label>
	<input type="text" name="link" id="link" class="input-xxlarge">
	{{ Form::actions(array(Button::primary_submit('Add Mod'))) }}
</form>
<script type="text/javascript">
$("#name").slugify('#pretty_name');
$(".modslug").slugify("#pretty_name");
$("#name").keyup(function() {
	$(".modslug").html($(this).val());
});
</script>
@endsection