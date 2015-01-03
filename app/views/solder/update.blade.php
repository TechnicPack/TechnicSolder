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
		    Main Settings
		    </div>
		    <div class="panel-body">
		        <label>Current Version: <span class="label label-default">{{ $currentData['version'] }}</span></label><br>
		        <label>Current Commit: <span class="label label-default">{{ $currentData['commit'] }}</span></label><br>
		        <label>Latest Version: <span class="label label-default">{{ $currentData['version'] }}</span></label><br>
		        <label>Latest Commit: <span class="label label-default">{{ $currentData['commit'] }}</span></label>
			</div>
		</div>
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