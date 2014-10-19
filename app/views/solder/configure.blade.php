@layout('layouts/master')
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

		<div class="form-group">
        <label for="mirror_url">Mirror URL</label>
        <input type="text" class="form-control" name="mirror_url" id="mirror_url" value="{{ Config::get('solder.mirror_url') }}" disabled>
        <span class="help-block">This is where the launcher will be told to search for your files. If your repo location is already a URL you can use the same location here. Include a trailing slash!</span>
    </div>

    <div class="form-group">
        <label for="repo_location">Repository Location</label>
        <input type="text" class="form-control" name="repo_location" id="repo_location" value="{{ Config::get('solder.repo_location') }}" disabled>
        <span class="help-block">This is the location of your mod reposistory. INCLUDE a trailing slash! This can be a URL or an absolute file location. This is only required for your initial repository import.</span>
    </div>

    <p>You can change these settings in <strong>application/config/solder.php</strong></p>
	</div>
</div>
@endsection