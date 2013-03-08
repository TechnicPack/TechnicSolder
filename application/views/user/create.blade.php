@layout('layouts/main')
@section('content')
<h1>User Management</h1>
<hr>
<h2>Create a New User</h2>
{{ Form::horizontal_open() }}
{{ Form::hidden("edit-user", 1) }}
{{ Form::control_group(Form::label('email', 'Email Address'), Form::xxlarge_text('email')) }}
{{ Form::control_group(Form::label('username', 'Username'), Form::xxlarge_text('username')) }}
{{ Form::control_group(Form::label('password', 'Password'), Form::xxlarge_password('password1')) }}
<hr>
<h3>Permissions</h3>
<p>
    Please select the level of access this user will be given. Giving a user any permission under "Solderwide" will give
    them full access across the entire system.
</p>
<div class="control-group">
    <label class="control-label">Solderwide</label>
    <div class="controls">
        <label for="solder-full"><input type="checkbox" name="solder-full" id="solder-full" value="1"> Full Solder Access</label>
        <label for="manage-users"><input type="checkbox" name="manage-users" id="manage-users" value="1"> Manage Users</label>
        <label for="manage-packs"><input type="checkbox" name="manage-packs" id="manage-packs" value="1"> Manage Modpacks</label>
        <label for="manage-mods"><input type="checkbox" name="manage-mods" id="manage-mods" value="1"> Manage Mods</label>
    </div>
</div>
<div class="control-group">
    <label class="control-label">Mod Library</label>
    <div class="controls">
        <label for="mod-create"><input type="checkbox" name="mod-create" id="mod-create" value="1"> Create Mods</label>
        <label for="mod-manage"><input type="checkbox" name="mod-manage" id="mod-manage" value="1"> Manage Mods</label>
        <label for="mod-delete"><input type="checkbox" name="mod-delete" id="mod-delete" value="1"> Delete Mods</label>
    </div>
</div>
<div class="control-group">
    <label class="control-label">Modpack Access</label>
    <div class="controls">
        @foreach (Modpack::all() as $modpack)
            <label for="{{ $modpack->slug }}"><input type="checkbox" name="modpack[]" id="{{ $modpack->slug }}" value="{{ $modpack->id }}"> {{ $modpack->name }}</label>
        @endforeach
    </div>
</div>
{{ Form::actions(array(Button::primary_submit('Save changes'),Button::link(URL::to('user/list'),'Go Back'))) }}
{{ Form::close() }}
@endsection