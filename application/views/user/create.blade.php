@layout('layouts/master')
@section('content')
<div class="page-header">
<h1>User Management</h1>
</div>
<div class="panel panel-default">
    <div class="panel-heading">
    Create User
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
        {{ Form::hidden("edit-user", 1) }}
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="text" class="form-control" name="email" id="email">
                </div>
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" class="form-control" name="username" id="username">
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" class="form-control" name="password" id="password">
                </div>
            </div>
            <div class="col-md-6">
                <h3>Permissions</h3>
                <p>
                    Please select the level of access this user will be given. The "Solderwide" permission is required to access a specific section. (Ex. Manage Modpacks is required for anyone to access even the list of modpacks. They will also need the respective permission for each modpack they should have access to.)
                </p>
                <div class="form-group">
                    <label class="control-label">Solderwide</label>
                    <div class="controls">
                        <label for="solder-full" class="checkbox-inline"><input type="checkbox" name="solder-full" id="solder-full" value="1"> Full Solder Access</label>
                        <label for="manage-users" class="checkbox-inline"><input type="checkbox" name="manage-users" id="manage-users" value="1"> Manage Users</label>
                        <label for="manage-packs" class="checkbox-inline"><input type="checkbox" name="manage-packs" id="manage-packs" value="1"> Manage Modpacks</label>
                        <label for="manage-mods" class="checkbox-inline"><input type="checkbox" name="manage-mods" id="manage-mods" value="1"> Manage Mods</label>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label">Mod Library</label>
                    <div class="controls">
                        <label for="mod-create" class="checkbox-inline"><input type="checkbox" name="mod-create" id="mod-create" value="1"> Create Mods</label>
                        <label for="mod-manage" class="checkbox-inline"><input type="checkbox" name="mod-manage" id="mod-manage" value="1"> Manage Mods</label>
                        <label for="mod-delete" class="checkbox-inline"><input type="checkbox" name="mod-delete" id="mod-delete" value="1"> Delete Mods</label>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label">Modpack Access</label>
                    <div class="controls">
                        <label for="solder-create" class="checkbox-inline"><input type="checkbox" name="solder-create" id="solder-create" value="1"> Create Modpacks</label>
                        @foreach (Modpack::all() as $modpack)
                            <label for="{{ $modpack->slug }}" class="checkbox-inline"><input type="checkbox" name="modpack[]" id="{{ $modpack->slug }}" value="{{ $modpack->id }}"> {{ $modpack->name }}</label>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        {{ Form::actions(array(Button::primary_submit('Create User'),Button::link(URL::to('user/list'),'Go Back'))) }}
        {{ Form::close() }}
    </div>
</div>
@endsection