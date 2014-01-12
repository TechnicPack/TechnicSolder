@layout('layouts/modpack')
@section('content')
<h1>Modpack Management</h1>
<hr>
<h2>Create Modpack</h2>
<p>Creating a modpack is simple. Fill in the information here and make sure you have the corresponding folder created on your repository with the necessary files. </p>
<blockquote>/<span class="modslug">[modslug]</span>/
</blockquote>
@if ($errors->all())
	<div class="alert alert-error">
	@foreach ($errors->all() as $error)
		{{ $error }}<br />
	@endforeach
	</div>
@endif
{{ Form::horizontal_open() }}
{{ Form::control_group(Form::label('name', 'Modpack Name'), Form::xxlarge_text('name'),'',Form::block_help('The Modpack-Name must match the modpacks name on the Platform for successful connect.')) }}
{{ Form::control_group(Form::label('slug', 'Modpack Slug'), Form::xxlarge_text('slug')) }}
{{ Form::actions(array(Button::primary_submit('Create Modpack'))) }}
{{ Form::close() }}
<script type="text/javascript">
$("#slug").slugify('#name');
$(".modslug").slugify("#name");
$("#slug").keyup(function() {
	$(".modslug").html($(this).val());
});
</script>
@endsection
