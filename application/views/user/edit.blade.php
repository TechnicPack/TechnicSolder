@layout('layouts/master')
@section('content')
<div class="page-header">
<h1>User Management</h1>
</div>
<div class="panel panel-default">
    <div class="panel-heading">
    Edit User: {{ $user->email }}
    </div>
    <div class="panel-body">
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
        {{ Form::open() }}
        {{ Form::hidden("edit-user", 1) }}
        <div class="row">
            <div class="col-md-6">
        
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="text" class="form-control" name="email" id="email" value="{{ $user->email }}">
                </div>
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" class="form-control" name="username" id="username" value="{{ $user->username }}">
                </div>
                <hr>
                <p>If you would like to change this accounts password you may include new passwords below. This is not required to edit an account</p>
                <div class="form-group">
                    <label for="password1">Password</label>
                    <input type="password" class="form-control" name="password1" id="password1">
                </div>
                <div class="form-group">
                    <label for="password2">Password Again</label>
                    <input type="password" class="form-control" name="password2" id="password2">
                </div>
            </div>
            <div class="col-md-6">
                @if (Auth::user()->permission->solder_full || Auth::user()->permission->solder_users)
                <h3>Permissions</h3>
                <p>
                    Please select the level of access this user will be given. The "Solderwide" permission is required to access a specific section. (Ex. Manage Modpacks is required for anyone to access even the list of modpacks. They will also need the respective permission for each modpack they should have access to.)
                </p>
                <div class="form-group">
                    <label>Solderwide</label>
                    <div class="controls">
                        <label for="solder-full" class="checkbox-inline"><input type="checkbox" name="solder-full" id="solder-full" value="1"{{ $checked = ($user->permission->solder_full ? " checked" : "") }}> Full Solder Access (Blanket permission)</label>
                        <label for="manage-users" class="checkbox-inline"><input type="checkbox" name="manage-users" id="manage-users" value="1"{{ $checked = ($user->permission->solder_users ? " checked" : "") }}> Manage Users</label>
                        <label for="manage-packs" class="checkbox-inline"><input type="checkbox" name="manage-packs" id="manage-packs" value="1"{{ $checked = ($user->permission->solder_modpacks ? " checked" : "") }}> Manage Modpacks</label>
                        <label for="manage-mods" class="checkbox-inline"><input type="checkbox" name="manage-mods" id="manage-mods" value="1"{{ $checked = ($user->permission->solder_mods ? " checked" : "") }}> Manage Mods</label>
                    </div>
                </div>
                <div class="form-group">
                    <label>Mod Library</label>
                    <div class="controls">
                        <label for="mod-create" class="checkbox-inline"><input type="checkbox" name="mod-create" id="mod-create" value="1"{{ $checked = ($user->permission->mods_create ? " checked" : "") }}> Create Mods</label>
                        <label for="mod-manage" class="checkbox-inline"><input type="checkbox" name="mod-manage" id="mod-manage" value="1"{{ $checked = ($user->permission->mods_manage ? " checked" : "") }}> Manage Mods</label>
                        <label for="mod-delete" class="checkbox-inline"><input type="checkbox" name="mod-delete" id="mod-delete" value="1"{{ $checked = ($user->permission->mods_delete ? " checked" : "") }}> Delete Mods</label>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">Modpack Access</label>
                    <div class="controls">
                        <label for="solder-create" class="checkbox-inline"><input type="checkbox" name="solder-create" id="solder-create" value="1"{{ $checked = ($user->permission->solder_create ? " checked" : "") }}> Create Modpacks</label>
                        @foreach (Modpack::all() as $modpack)
                            <label for="{{ $modpack->slug }}" class="checkbox-inline"><input type="checkbox" name="modpack[]" id="{{ $modpack->slug }}" value="{{ $modpack->id }}"{{ $checked = (in_array($modpack->id, $user->permission->modpacks) ? " checked" : "") }}> {{ $modpack->name }}</label>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
        <?php echo Form::actions(array(
        			Button::primary_submit('Save changes'),
        			Button::link(URL::to('user/list'),'Go Back')
        			)) ?>
        {{ Form::close() }}
    </div>
</div>
@endsection