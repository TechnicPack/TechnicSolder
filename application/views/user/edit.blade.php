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
<hr>
@if (Auth::user()->permission->solder_full || Auth::user()->permission->solder_users)
<h3>Permissions</h3>
<p>
    Please select the level of access this user will be given. The "Solderwide" permission is required to access a specific section. (Ex. Manage Modpacks is required for anyone to access even the list of modpacks. They will also need the respective permission for each modpack they should have access to.)
</p>
<div class="control-group">
    <label class="control-label">Solderwide</label>
    <div class="controls">
        <label for="solder-full"><input type="checkbox" name="solder-full" id="solder-full" value="1"{{ $checked = ($user->permission->solder_full ? " checked" : "") }}> Full Solder Access (Blanket permission)</label>
        <label for="manage-users"><input type="checkbox" name="manage-users" id="manage-users" value="1"{{ $checked = ($user->permission->solder_users ? " checked" : "") }}> Manage Users</label>
        <label for="manage-packs"><input type="checkbox" name="manage-packs" id="manage-packs" value="1"{{ $checked = ($user->permission->solder_modpacks ? " checked" : "") }}> Manage Modpacks</label>
        <label for="manage-mods"><input type="checkbox" name="manage-mods" id="manage-mods" value="1"{{ $checked = ($user->permission->solder_mods ? " checked" : "") }}> Manage Mods</label>
    </div>
</div>
<div class="control-group">
    <label class="control-label">Mod Library</label>
    <div class="controls">
        <label for="mod-create"><input type="checkbox" name="mod-create" id="mod-create" value="1"{{ $checked = ($user->permission->mods_create ? " checked" : "") }}> Create Mods</label>
        <label for="mod-manage"><input type="checkbox" name="mod-manage" id="mod-manage" value="1"{{ $checked = ($user->permission->mods_manage ? " checked" : "") }}> Manage Mods</label>
        <label for="mod-delete"><input type="checkbox" name="mod-delete" id="mod-delete" value="1"{{ $checked = ($user->permission->mods_delete ? " checked" : "") }}> Delete Mods</label>
    </div>
</div>
<div class="control-group">
    <label class="control-label">Modpack Access</label>
    <div class="controls">
        <label for="solder-create"><input type="checkbox" name="solder-create" id="solder-create" value="1"{{ $checked = ($user->permission->solder_create ? " checked" : "") }}> Create Modpacks</label>
        @foreach (Modpack::all() as $modpack)
            <label for="{{ $modpack->slug }}"><input type="checkbox" name="modpack[]" id="{{ $modpack->slug }}" value="{{ $modpack->id }}"{{ $checked = (in_array($modpack->id, $user->permission->modpacks) ? " checked" : "") }}> {{ $modpack->name }}</label>
        @endforeach
    </div>
</div>
@endif
<?php echo Form::actions(array(
			Button::primary_submit('Save changes'),
			Button::link(URL::to('user/list'),'Go Back')
			)) ?>
{{ Form::close() }}
@endsection