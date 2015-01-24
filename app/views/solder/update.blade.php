@extends('layouts/master')
@section('content')
<div class="row">
	<div class="page-header">
	<h1>Update Manager</h1>
	</div>
</div>
<div class="row">
	<div class="col-lg-6">
		<div class="panel panel-default">
			<div class="panel-heading">
		    Solder Versioning
		    </div>
		    <div class="panel-body">
                @if (!Cache::get('checker'))
                <div class="alert alert-danger">Update Checker is disabled. Latest info from Github is displayed instead.</div>
                @endif
		        <label>Current Version: <span class="label label-default">{{ $currentData['version'] }}</span></label><br>
                @if (Cache::get('checker'))
		        <label>Current Commit: <span class="label label-default">{{ $currentData['commit'] }}</span></label><br>
                <label>Current Branch: <span class="label label-default">{{ $currentData['branch'] }}</span></label><br>
                @endif
		        <label>Latest Version: <span class="label label-default">{{ $latestData['version'] }}</span></label><br>
		        <label>Latest Commit: <span class="label label-default">{{ $latestData['commit']['sha'] }}</span></label><br>
                <label>Shell Access: {{ $currentData['shell_exec'] ? "<span class='label label-success'> Yes" : "<span class='label label-danger'> No" }}</span></label><br>
                <label>Git Installed: {{ $currentData['git'] ? "<span class='label label-success'> Yes" : "<span class='label label-danger'> No" }}</span></label><br>
                <label>Git Repository Found: {{ $currentData['gitrepo'] ? "<span class='label label-success'> Yes" : "<span class='label label-danger'> No" }}</span></label><br>
			</div>
		</div>
        @if (Cache::get('checker'))
        <div class="panel panel-default">
            <div class="panel-heading">
            Update Check
            </div>
            <div class="panel-body">
                @if (Cache::has('update') && Cache::get('update'))
                <p id='solder-update-ajax' class="alert alert-danger">Solder is out of date. Please refer to the wiki on how to update.</p>                
                @elseif (Cache::get('checker') && strcasecmp($currentData['commit'], $latestData['commit']['sha']) != 0 && $currentData['branch'] == 'master')
                <p id='solder-update-ajax' class="alert alert-warning">Solder is up to date, but hotfixes are available</p>
                @elseif (Cache::get('checker') && strcasecmp($currentData['commit'], $latestData['commit']['sha']) != 0 && $currentData['branch'] == 'dev')
                <p id='solder-update-ajax' class="alert alert-warning">Solder is up to date, but dev builds are available</p>
                @else
                <p id='solder-update-ajax' class="alert alert-success">Solder is up to date</p>
                @endif
                <a href="https://github.com/TechnicPack/TechnicSolder/wiki/Updating-Solder#updating-from-v07-onward" target="blank_"><button id='solder-wiki' class="btn btn-default">Updating Solder <i class="fa fa-question"></i></button></a>
                <button id='solder-update' type="submit" class="btn btn-default">Check for update</button>
                <span id="solder-checking" style="margin-left:10px;" class="hidden"><i class="fa fa-cog fa-spin"></i> Checking...</span>
            </div>
        </div>
        @endif
	</div>
	<div class="col-lg-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-clock-o fa-fw"></i> Activity Panel</h3>
            </div>
            <div class="panel-body">
                <div class="list-group">
                	@foreach ($changelog as $change)
                    <a href="{{ $change['html_url'] }}" target="blank_" class="list-group-item">
                        <span class="badge" style="margin-left:5px;">{{  date_format(date_create($change['commit']['author']['date']), 'M, d, Y | g:i a') }}</span>
                        <img src="{{ $change['author']['avatar_url']}}" alt="{{ $change['author']['login']}}" height="23" width="23"> {{ $change['commit']['message'] }}
                    </a>
                    @endforeach                    
                </div>
                <div class="text-right">
                    <a href="https://github.com/TechnicPack/TechnicSolder/commits/master">View All Activity <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('bottom')
<script type="text/javascript">

$('#solder-update').click(function(e) {
    $("#solder-checking").removeClass("hidden");
    $("#solder-update-ajax").fadeOut();
    e.preventDefault();
    $.ajax({
        type: "GET",
        url: "{{ URL::to('solder/update-check/') }}/",
        success: function (data) {
            if (data.status == "success") {
                if(data.update) {
                    $("#solder-update-ajax").removeClass("alert-warning alert-success alert-danger").addClass("alert-danger").html('Solder is out of date. Please refer to the wiki on how to update.').fadeIn();
                } else {
                    if(data.build) {
                        if(data.branch == 'dev'){
                            $("#solder-update-ajax").removeClass("alert-warning alert-success alert-danger").addClass("alert-warning").html('Solder is up to date, but dev builds are available').fadeIn();
                        } else {
                            $("#solder-update-ajax").removeClass("alert-warning alert-success alert-danger").addClass("alert-warning").html('Solder is up to date, but hotfixes are available').fadeIn();
                        }
                    } else {
                        $("#solder-update-ajax").removeClass("alert-warning alert-success alert-danger").addClass("alert-success").html('Solder is up to date.').fadeIn();
                    }
                }
            } else {
                $("#solder-update-ajax").removeClass("alert-warning alert-success alert-danger").addClass("alert-danger").html('Error checking for update. ' + data.reason);
            }
            $("#solder-checking").addClass("hidden");
        },
        error: function (xhr, textStatus, errorThrown) {
            $("#solder-update-ajax").removeClass("alert-warning alert-success alert-danger").addClass("alert-danger").html('Error checking for update. ' + textStatus + ': ' + errorThrown);
            $("#solder-checking").addClass("hidden");
        }
    });
});

</script>
@endsection