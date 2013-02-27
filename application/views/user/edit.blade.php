@layout('layouts/main');
@section('content')
<h1>User Management</h1>
<hr>
<h2>Edit User: {{ $user->email }}</h2>
<hr>
@if ($errors->all())
	<div class="alert alert-error">
	@foreach ($errors->all() as $error)
		{{ $error }}<br />
	@endforeach
	</div>
@endif
@if (Session::has('success'))
	<div class="alert alert-success">
		{{ Session::get('success') }}
	</div>
@endif
{{ Form::horizontal_open() }}
{{ Form::hidden("edit-user", 1) }}
{{ Form::control_group(Form::label('email', 'Email Address'), Form::xxlarge_text('email', $user->email)) }}
{{ Form::control_group(Form::label('username', 'Username'), Form::xxlarge_text('username', $user->username)) }}
<hr>
<p>If you would like to this accounts password you may include new passwords below. This is not required to edit an account</p>
{{ Form::control_group(Form::label('password1', 'Password'), Form::xxlarge_password('password1')) }}
{{ Form::control_group(Form::label('password2', 'Password Again'), Form::xxlarge_password('password2')) }}
<?php echo Form::actions(array(
			Button::primary_submit('Save changes'),
			Button::link(URL::to('user/list'),'Go Back')
			)) ?>
@endsection