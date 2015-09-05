@extends('layouts/master')
@section('title')
	<title>Bulk Upload Mods - TechnicSolder</title>
@stop
@section('content')
<div class="page-header">
<h1>Mod Library</h1>
</div>
<div class="panel panel-default">
	<div class="panel-heading">
	Bulk Upload Mods
	</div>
	<div class="panel-body">
		@if ($errors->all())
			<div class="alert alert-danger">
			@foreach ($errors->all() as $error)
				{{ $error }}<br />
			@endforeach
			</div>
		@endif
		@if (Session::get('notices'))
			<div class="alert alert-info">
			@foreach (Session::get('notices') as $notice)
				{{ $notice }}<br />
			@endforeach
			</div>
		@endif
		{{ Form::open(array('url'=>URL::to('mod/bulk'),'files'=>true)) }}
		<div class="row">
			<div class="col-md-3">
				<div class="form-group">
					{{ Form::label('csv', 'Bulk Upload CSV') }}
					{{ Form::file('csv', '', array('class'=>'form-controll')) }}
				</div>
			</div>
			<div class="col-md-9">
				<p>In order to have your file processed correctly it must end in .csv and follow the below example:</p>
				<code>pretty_name,name,author,description,link,donatelink<br/>Cool Mod,cool-mod,Bob,A really cool starter mod.,"http://example.com","http://example.com"</code><br/><br/>
				<p>Not having the first line with the column names will cause the file to break and there is currently no validation to make sure you put them in.</p>
			</div>
		</div>
		{{ Form::submit('Bulk Upload Mods', array('class' => 'btn btn-success')) }}
		{{ HTML::link('mod/list/', 'Go Back', array('class' => 'btn btn-primary')) }}
		{{ Form::close() }}
	</div>
</div>
@endsection
@section('bottom')
<script type="text/javascript">
$("#name").slugify('#pretty_name');
$(".modslug").slugify("#pretty_name");
$("#name").keyup(function() {
	$(".modslug").html($(this).val());
});
</script>
@endsection