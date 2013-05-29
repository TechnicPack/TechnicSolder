@layout('layouts/modpack')
@section('content')
<h1>Modpack Management</h1>
<hr>
<h2>Edit Modpack: {{ $modpack->name }}</h2>
<p>Editing a modpack requires that the resources exist just like when you create them. If the slug is changed, make sure to move the resources to the new area.</p>
@if ($errors->all())
	<div class="alert alert-error">
	@foreach ($errors->all() as $error)
		{{ $error }}<br />
	@endforeach
	</div>
@endif
{{ Form::horizontal_open() }}
{{ Form::control_group(Form::label('name', 'Modpack Name'), Form::xxlarge_text('name', $modpack->name)) }}
{{ Form::control_group(Form::label('slug', 'Modpack Slug'), Form::xxlarge_text('slug', $modpack->slug)) }}
<div class="control-group">
	<label class="control-label" for="hidden">Hide Modpack</label>
	<div class="controls">
		<input type="checkbox" name="hidden" id="hidden"{{ $checked = ($modpack->hidden ? ' checked' : '') }}>
	</div>
</div>
{{ Form::actions(array(Button::primary_submit('Edit Modpack'),Button::danger_link(URL::to('modpack/delete/'.$modpack->id),'Delete Modpack'))) }}
{{ Form::close() }}
<script type="text/javascript">
$("#slug").slugify('#name');
</script>
@endsection