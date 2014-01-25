@layout('layouts/master')
@section('content')
<div class="page-header">
<h1>Mod Library</h1>
</div>
<div class="panel panel-default">
	<div class="panel-heading">
	Add Mod
	</div>
	<div class="panel-body">
		@if ($errors->all())
			<div class="alert alert-error">
			@foreach ($errors->all() as $error)
				{{ $error }}<br />
			@endforeach
			</div>
		@endif
		<form method="post" action="{{ URL::to('mod/create') }}">
		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
                    <label for="pretty_name">Mod Name</label>
                    <input type="text" class="form-control" name="pretty_name" id="pretty_name">
                </div>
                <div class="form-group">
                    <label for="name">Mod Slug</label>
                    <input type="text" class="form-control" name="name" id="name">
                </div>
                <div class="form-group">
                    <label for="author">Author</label>
                    <input type="text" class="form-control" name="author" id="author">
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea name="description" id="description" class="form-control" rows="5"></textarea>
                </div>
                 <div class="form-group">
                    <label for="link">Mod Website</label>
                    <input type="text" class="form-control" name="link" id="link">
                </div>
			</div>
			<div class="col-md-6">
				<p>Because Solder doesn't do any file handling yet you will need to manually manage your set of mods in your repository. The mod repository structure is very strict and must match your Solder data exact. An example of your mod directory structure will be listed below:</p>
				<blockquote>/mods/<span class="modslug">[modslug]</span>/<br>
					/mods/<span class="modslug">[modslug]</span>/<span class="modslug">[modslug]</span>-[version].zip
				</blockquote>
			</div>
		</div>
		{{ Form::actions(array(Button::primary_submit('Add Mod'))) }}
		</form>
	</div>
</div>
@endsection
@section('bottom')
<script type="text/javascript">
$("#name").slugify('#pretty_name');
$(".modslug").slugify("#pretty_name");
$("#name").keyup(function() {
	$(".modslug").html($(this).val());
});
</script>
@endsection