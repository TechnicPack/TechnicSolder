@layout('layouts/main')
@section('content')
<h1>Configure Solder</h1>
<hr>
@if (Session::has('success'))
	<div class="alert alert-success">
		{{ Session::get('success') }}
	</div>
@endif
<h2>Repository Settings</h2>
{{ Form::horizontal_open() }}
{{ Form::hidden("edit-solder", 1) }}
<?php echo Form::control_group(Form::label('mirror_url', 'Mirror URL'),
   Form::xxlarge_text('mirror_url', Config::get('solder.mirror_url')), '', 
   Form::block_help('This is where the launcher will be told to search for your files. If your repo location is already a URL you can use the same location here. Include a trailing slash!'));

 echo Form::control_group(Form::label('repo_location', 'Repository Location'),
   Form::xxlarge_text('repo_location', Config::get('solder.repo_location')), '', 
   Form::block_help('This is the location of your mod reposistory. INCLUDE a trailing slash! This can be a URL or an absolute file location. This is only required for your initial repository import.')); ?>

<h2>Platform Settings</h2>

<?php echo Form::control_group(Form::label('platform_key', 'Platform API Key'),
   Form::xxlarge_text('platform_key', Config::get('solder.platform_key')), '', 
   Form::block_help(' Enter your platform API key if you would like to link Solder to your Platform account.'));

echo Form::actions(array(Button::primary_submit('Save changes')));
?>

{{ Form::close() }}
@endsection