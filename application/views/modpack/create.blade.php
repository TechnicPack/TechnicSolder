@layout('layouts/master')
@section('content')
<div class="page-header">
<h1>Modpack Management</h1>
</div>
<div class="panel panel-default">
	<div class="panel-heading">
	Create Modpack
	</div>
	<div class="panel-body">
		
		@if ($errors->all())
			<div class="alert alert-error">
			@foreach ($errors->all() as $error)
				{{ $error }}<br />
			@endforeach
			</div>
		@endif
		{{ Form::open() }}
		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
		            <label for="name">Modpack Name</label>
		            <input type="text" class="form-control" name="name" id="name">
		        </div>
		        <div class="form-group">
		            <label for="slug">Modpack Slug</label>
		            <input type="text" class="form-control" name="slug" id="slug">
		        </div>
		    </div>
		   	<div class="col-md-6">
		   		<p>Creating a modpack is simple. Fill in the information here and make sure you have the corresponding folder created on your repository with the necessary files. </p>
				<blockquote>/<span class="modslug">[modslug]</span>/
				</blockquote>
				<p>If you wish to link this modpack with an existing Technic Platform modpack, the slug must be identical to your slug on the Platform!</p>
		   	</div>
		</div>
		{{ Form::actions(array(Button::primary_submit('Create Modpack'))) }}
		{{ Form::close() }}
	</div>
</div>
<script type="text/javascript">
$("#slug").slugify('#name');
$(".modslug").slugify("#name");
$("#slug").keyup(function() {
	$(".modslug").html($(this).val());
});
</script>
@endsection