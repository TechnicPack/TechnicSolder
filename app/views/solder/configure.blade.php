@extends('layouts/master')
@section('title')
    <title>Main Settings - TechnicSolder</title>
@stop
@section('content')
<div class="page-header">
<h1>Configure Solder</h1>
</div>
<ul class="nav nav-tabs">
    <li class="active"><a href="#main" data-toggle="tab">Solder</a></li>
    <li><a href="#amazon" data-toggle="tab">Amazon S3</a></li>
</ul>
 <div class="tab-content">
    <div class="tab-pane fade in active" id="main">
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
                    <label for="mirror_url">Repository Mirror URL</label>
                    <input type="text" class="form-control" name="mirror_url" id="mirror_url" value="{{ Config::get('solder.mirror_url') }}" disabled>
                    <span class="help-block">This is the public facing URL for your repo. If your repository location is already a URL, you can use the same location here. Include a trailing slash!</span>
                </div>

                <div class="form-group">
                    <label for="repo_location">Repository Location</label>
                    <input type="text" class="form-control" name="repo_location" id="repo_location" value="{{ Config::get('solder.repo_location') }}" disabled>
                    <span class="help-block">This is the location of your mod reposistory. This can be a URL or an absolute file location(faster). When an absolute file location is used, Solder will attempt to calculate the MD5 checksum internally instead of over the remote web request.</span>
                    <p class="alert alert-warning">The Repo Location is the prime suspect when MD5 hashing fails. Most cases are caused by improper file permissions when using an absolute file location</p>
                </div>

                <div class="form-group">
                    <label for="md5hashtimeout">MD5 Hashing Timeout</label>
                    <input type="text" class="form-control" name="md5hashtimeout" id="md5hashtimeout" value="{{ Config::get('solder.md5filetimeout') }}" disabled>
                    <span class="help-block">This is the amount of time (in seconds) Solder will attempt to hash a mod before timing out.</span>
                </div>

                <p>You can change these settings in <strong>app/config/solder.php</strong></p>
        	</div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
            Minecraft Versions Caching 
            </div>
            <div class="panel-body">
            <p>Solder now caches a list of minecraft versions and their MD5 checksums. First attempt is from the Technic Platform. If that fails, an internal library processes
            the list of versions from Mojang and calculates their MD5 checksums. The result from either is then cached. You can manually update the cache below.</p>
            <hr>
            @if (Cache::has('minecraftversions'))
                <p id='minecraft-ajax' class="alert alert-success">Minecraft versions are currently cached.</p>
                <button id='minecraft-cache' type="submit" class="btn btn-default">Update Cache</button>
                <span id="mc-loading" style="margin-left:10px;" class="hidden"><i class="fa fa-cog fa-spin"></i> Caching...</span>
            @else
                <p id='minecraft-ajax' class="alert alert-warning">Minecraft versions are not cached. This may cause unexpectedly long page loads</p>
                <button id='minecraft-cache' type="submit" class="btn btn-default">Cache</button>
                <span id="mc-loading" style="margin-left:10px;" class="hidden"><i class="fa fa-cog fa-spin"></i> Caching...</span>
            @endif
            </div>
        </div>
    </div>
    <div class="tab-pane fade" id="amazon">
        <div class="panel panel-default">
            <div class="panel-heading">
                Amazon S3
            </div>
            <div class="panel-body">
                @if (Session::has('success'))
                <div class="alert alert-success">
                    {{ Session::get('success') }}
                </div>
                @endif

                <label>Use Amazon S3: {{ Config::get('solder.use_s3') ? "<span class='label label-success'> Yes" : "<span class='label label-danger'> No" }}</span></label><br><br>

                <div class="form-group">
                    <label for="access_key">Amazon AWS Access Key</label>
                    <input type="text" class="form-control" name="access_key" id="access_key" value="{{ Config::get('solder.access_key') }}" disabled>
                </div>

                <div class="form-group">
                    <label for="secret_key">Amazon AWS Secret Key</label>
                    <input type="text" class="form-control" name="secret_key" id="secret_key" value="{{ Config::get('solder.secret_key') }}" disabled>
                </div>

                <div class="form-group">
                    <label for="bucket">Amazon S3 Bucket</label>
                    <input type="text" class="form-control" name="bucket" id="bucket" value="{{ Config::get('solder.bucket') }}" disabled>
                    <span class="help-block">This is the bucket that will be used to store your pack resources.</span>
                </div>

                <p>You can change these settings in <strong>app/config/solder.php</strong></p>
            </div>
        </span>
    </div>
@endsection
@section('bottom')
<script type="text/javascript">

$('#minecraft-cache').click(function(e) {
    $("#mc-loading").removeClass("hidden");
    e.preventDefault();
    $.ajax({
        type: "GET",
        url: "{{ URL::to('solder/cache-minecraft/') }}/",
        success: function (data) {
            if (data.status == "success") {
                console.log(data.reason);
                $("#minecraft-ajax").removeClass("alert-warning alert-success alert-danger").addClass("alert-success").html('Minecraft version caching complete.');
                $("#minecraft-cache").html('Update Cache');
                $("#mc-loading").addClass("hidden");
            } else {
                $("#minecraft-ajax").removeClass("alert-warning alert-success alert-danger").addClass("alert-danger").html('Error caching Minecraft versions. ' + data.reason);
                $("#minecraft-cache").html('Update Cache');
                $("#mc-loading").addClass("hidden");
            }
        },
        error: function (xhr, textStatus, errorThrown) {
            $("#minecraft-ajax").removeClass("alert-warning alert-success alert-danger").addClass("alert-danger").html('Error caching Minecraft versions. ' + textStatus + ': ' + errorThrown);
            $("#minecraft-cache").html('Update Cache');
            $("#mc-loading").addClass("hidden");
        }
    });
});

</script>
@endsection