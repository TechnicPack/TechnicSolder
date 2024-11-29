@extends('layouts/master')
@section('title')
    <title>Main Settings - Technic Solder</title>
@stop
@section('content')
    <div class="page-header">
        <h1>Configure Solder</h1>
    </div>
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
                        <input type="text"
                               class="form-control"
                               name="mirror_url"
                               id="mirror_url"
                               value="{{ config('solder.mirror_url') }}"
                               disabled
                        >
                        <span class="help-block">This is the public facing URL for your repository. If your repository
                            location is already a URL, you can use the same value here. Include a trailing slash!
                        </span>
                    </div>

                    <div class="form-group">
                        <label for="repo_location">Repository Location</label>
                        <input type="text"
                               class="form-control"
                               name="repo_location"
                               id="repo_location"
                               value="{{ config('solder.repo_location') }}"
                               disabled
                        >
                        <span class="help-block">This is the location of your mod repository. This can be a URL (remote
                            repo), or an absolute file location (local repo, much faster). When a remote repo is used,
                            Solder will have to download the entire file to calculate the MD5 hash.
                        </span>
                        <p class="alert alert-info">The repository location is the prime suspect when MD5 hashing fails.
                            Most cases are caused by improper file permissions when using an absolute file location.</p>
                    </div>

                    <div class="form-group">
                        <label for="md5_connect_timeout">Remote MD5 Connect Timeout</label>
                        <input type="text"
                               class="form-control"
                               name="md5_connect_timeout"
                               id="md5_connect_timeout"
                               value="{{ config('solder.md5_connect_timeout') }}"
                               disabled
                        >
                        <span class="help-block">This is the amount of time (in seconds) Solder will wait before giving
                            up trying to connect to a URL to hash a mod.
                        </span>
                    </div>

                    <div class="form-group">
                        <label for="md5_file_timeout">Remote MD5 Total Timeout</label>
                        <input type="text"
                               class="form-control"
                               name="md5_file_timeout"
                               id="md5_file_timeout"
                               value="{{ config('solder.md5_file_timeout') }}"
                               disabled
                        >
                        <span class="help-block">This is the amount of time (in seconds) Solder will attempt to remotely
                            hash a mod for before giving up.
                        </span>
                    </div>

                    <p>You can change these settings in the <strong>.env</strong> file.</p>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    Minecraft Versions Caching
                </div>
                <div class="panel-body">
                    <p>Solder caches a list of Minecraft versions. The first attempt is from the Technic Platform. If
                        that fails, it tries to get them from Mojang. The result is then cached for 3 hours. You can
                        manually update the cache below.</p>
                    <hr>
                    @if (Cache::has('minecraftversions'))
                        <p id='minecraft-ajax' class="alert alert-success">Minecraft versions are currently cached.</p>
                        <button id='minecraft-cache' type="submit" class="btn btn-default">Update Cache</button>
                        <span id="mc-loading" style="margin-left:10px;" class="hidden"><i class="fa fa-cog fa-spin"></i>
                            Caching...
                        </span>
                    @else
                        <p id='minecraft-ajax' class="alert alert-warning">Minecraft versions are not cached. This may
                            cause unexpectedly long page loads the first time it loads them.</p>
                        <button id='minecraft-cache' type="submit" class="btn btn-default">Cache</button>
                        <span id="mc-loading" style="margin-left:10px;" class="hidden"><i class="fa fa-cog fa-spin"></i>
                            Caching...
                        </span>
                    @endif
                </div>
            </div>
        </div>
        @endsection
        @section('bottom')
            <script type="text/javascript">

                $('#minecraft-cache').click(function (e) {
                    $("#mc-loading").removeClass("hidden");
                    e.preventDefault();
                    $.ajax({
                        type: "GET",
                        url: "{{ URL::to('solder/cache-minecraft/') }}/",
                        success: function (data) {
                            if (data.success) {
                                console.log(data.reason);
                                $("#minecraft-ajax").removeClass("alert-warning alert-success alert-danger").addClass("alert-success").html('Minecraft version caching complete.');
                                $("#minecraft-cache").html('Update Cache');
                                $("#mc-loading").addClass("hidden");
                            } else {
                                $("#minecraft-ajax").removeClass("alert-warning alert-success alert-danger").addClass("alert-danger").html('Error caching Minecraft versions: ' + data.message);
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