@extends('layouts/master')
@section('title')
    <title>Create User - TechnicSolder</title>
@stop
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
            <div class="alert alert-danger">
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
                {{ Form::submit('Create User', array('class' => 'btn btn-success')) }}
            </div>
            <div class="col-md-6">
                <h3>Permissions</h3>
                <p>
                    Please select the level of access this user will be given. The "Solderwide" permission is required to access a specific section. Mod and Modpack user permissions are displayed in there corresponding sections.
                </p>
                <div class="form-group">
                    <label>Solderwide</label>
                    <div class="controls">
                        <label for="solder-full" class="checkbox-inline"><input type="checkbox" name="solder-full" id="solder-full" value="1"> Full Solder Access (Blanket permission)</label>
                        <label for="manage-users" class="checkbox-inline"><input type="checkbox" name="manage-users" id="manage-users" value="1"> Manage Users</label>
                        <label for="manage-keys" class="checkbox-inline"><input type="checkbox" name="manage-keys" id="manage-keys" value="1"> Manage API Keys</label>
                        <label for="manage-clients" class="checkbox-inline"><input type="checkbox" name="manage-clients" id="manage-clients" value="1"> Manage Clients</label>
                    </div>
                </div>
                <div class="form-group">
                    <label>Mod Library</label>
                    <div class="controls">
                        <label for="mod-create" class="checkbox-inline"><input type="checkbox" name="mod-create" id="mod-create" value="1"> Create Mods</label>
                        <label for="mod-manage" class="checkbox-inline"><input type="checkbox" name="mod-manage" id="mod-manage" value="1"> Manage Mods</label>
                        <label for="mod-delete" class="checkbox-inline"><input type="checkbox" name="mod-delete" id="mod-delete" value="1"> Delete Mods</label>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label">General Modpack Access</label>
                    <p>General Modpack Access permissions are required before granting access to a specific modpack. Users without these permission will not be able to perform stated actions even if the specfic modpack is selected.</p>
                    <div class="controls">
                        <label for="modpack-create" class="checkbox-inline"><input type="checkbox" name="modpack-create" id="modpack-create" value="1"> Create Modpacks</label>
                        <label for="modpack-manage" class="checkbox-inline"><input type="checkbox" name="modpack-manage" id="modpack-manage" value="1"> Manage Modpacks</label>
                        <label for="modpack-delete" class="checkbox-inline"><input type="checkbox" name="modpack-delete" id="modpack-delete" value="1"> Delete Modpacks</label>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label">Specific Modpacks</label>
                    <div class="controls">
                        @foreach (Modpack::all() as $modpack)
                            <label for="{{ $modpack->slug }}" class="checkbox-inline"><input type="checkbox" name="modpack[]" id="{{ $modpack->slug }}" value="{{ $modpack->id }}"> {{ $modpack->name }}</label>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        {{ Form::close() }}
    </div>
</div>
@endsection