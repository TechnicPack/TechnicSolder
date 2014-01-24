@layout('layouts/main')
@section('content')
<div class="page-header">
<h1>Configure Solder</h1>
</div>
<div class="panel panel-default">
  <div class="panel-heading">
  Main Settings
  </div>
  <div class="panel-body">
		@if (Session::has('success'))
			<div class="alert alert-success">
				{{ Session::get('success') }}
			</div>
		@endif
		<h3>Repository Settings</h3>
		{{ Form::open() }}
		{{ Form::hidden("edit-solder", 1) }}

		<div class="form-group">
            <label for="mirror_url">Mirror URL</label>
            <input type="text" class="form-control" name="mirror_url" id="mirror_url" value="{{ Config::get('solder.mirror_url') }}">
            <span class="help-block">This is where the launcher will be told to search for your files. If your repo location is already a URL you can use the same location here. Include a trailing slash!</span>
        </div>

        <div class="form-group">
            <label for="repo_location">Repository Location</label>
            <input type="text" class="form-control" name="repo_location" id="repo_location" value="{{ Config::get('solder.repo_location') }}">
            <span class="help-block">This is the location of your mod reposistory. INCLUDE a trailing slash! This can be a URL or an absolute file location. This is only required for your initial repository import.</span>
        </div>

		<h3>Platform Settings</h3>

		<div class="form-group">
            <label for="platform_key">Platform API Key</label>
            <input type="text" class="form-control" name="platform_key" id="platform_key" value="{{ Config::get('solder.platform_key') }}">
            <span class="help-block">Enter your platform API key if you would like to link Solder to your Platform account.</span>
        </div>

		{{ Form::actions(array(Button::primary_submit('Save changes'))) }}

		{{ Form::close() }}
	</div>
</div>
@endsection