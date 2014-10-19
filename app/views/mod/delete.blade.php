@layout('layouts/master')
@section('content')
<div class="page-header">
<h1>Mod Library</h1>
</div>
<div class="panel panel-default">
  <div class="panel-heading">
  Delete Request for {{ $mod->name }}
  </div>
  <div class="panel-body">
    <p>Deleting a mod can have serious reprecussions. All associated modpacks and builds using this mod will have the mod forcefully stripped from their build history. This includes every version of the mod in every build it existed in. This tool should mainly be used to delete a mod you created by mistake or are entirely sure is no longer needed. For your convenience we have generated a list of all builds currently using this mod for every version of the mod. <strong>Please carefully check the versions list before removing the mod!</strong></p>
    <p>Deleting a mod is irreverisble!</p>
    <div class="accordion" id="versionsexp">
      <div class="accordion-group">
        <div class="accordion-heading">
          <a class="accordion-toggle" data-toggle="collapse" data-parent="#versionsexp" href="#versions">
            Versions
          </a>
        </div>
        <div id="versions" class="accordion-body collapse">
          <div class="accordion-inner">
            <ul>
    		@foreach ($mod->versions as $ver)
    			<li>{{ $ver->version }}</li>
    			@if (count($ver->builds) >= 1)
    				<ul>
    					@foreach ($ver->builds as $build)
    						<li>{{ $build->modpack->name }} - {{ $build->version }}</li>
    					@endforeach
    				</ul>
    			@endif
    		@endforeach
    		</ul>
          </div>
        </div>
      </div>
    </div>
    {{ Form::open() }}
    <hr>
    <button type="submit" class="btn btn-danger">Confirm Deletion</button>  <a href="{{ Request::referrer() }}" class="btn">Go Back</a>
    {{ Form::close() }}
  </div>
</div>
@endsection