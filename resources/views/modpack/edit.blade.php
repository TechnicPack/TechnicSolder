@extends('layouts/master')
@section('title')
    <title>{{ $modpack->name }} - Technic Solder</title>
@stop
@section('content')
    <div class="page-header">
        <h1>Modpack Management - {{ $modpack->name }}</h1>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            Editing Modpack: {{ $modpack->name }}
        </div>
        <div class="panel-body">
            @if ($errors->all())
                <div class="alert alert-danger">
                    @foreach ($errors->all() as $error)
                        {{ $error }}<br/>
                    @endforeach
                </div>
            @endif
            <form method="post" action="{{ url()->current() }}" accept-charset="UTF-8">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <h3>Modpack Management</h3>
                        <p>Edit your modpack settings here. You are required to delete and re-import your pack on the
                            Technic Platform when changing the Modpack slug</p>
                        <hr>
                        <div class="form-group">
                            <label for="name">Modpack Name</label>
                            <input type="text" class="form-control" name="name" id="name" value="{{ $modpack->name }}">
                        </div>
                        <div class="form-group">
                            <label for="slug">Modpack Slug</label>
                            <input type="text" class="form-control" name="slug" id="slug" value="{{ $modpack->slug }}">
                        </div>
                        <hr>
                        <div class="form-group">
                            <label class="control-label" for="hidden">Hide Modpack</label>
                            <div class="controls">
                                <input type="checkbox"
                                       name="hidden"
                                       id="hidden"{{ $checked = ($modpack->hidden ? ' checked' : '') }}>
                                <span class="help-block">Hidden modpacks will not show up in the API response for the
                                    modpack list regardless of whether or not a client has access to the modpack.
                                </span>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="private">Private Modpack</label>
                            <div class="controls">
                                <input type="checkbox"
                                       name="private"
                                       id="private"{{ ($modpack->private ? ' checked' : '') }}>
                                <span class="help-block">Private modpacks will only be available to clients that are
                                    linked to this modpack. You can link clients below. You can also individually mark
                                    builds as private.
                                </span>
                            </div>
                        </div>
                        @if ($modpack->private || $modpack->private_builds())
                            <hr>
                            <h3>Client Access</h3>
                            <p>Check the clients below you want to have access to this modpack if anything is set to
                                private.</p>
                            @empty ($allClients)
                                <div class="alert alert-warning">No Clients to add</div>
                            @else
                                @foreach ($allClients as $client)
                                    <div style="display: inline-block; padding-right: 10px;">
                                        <input type="checkbox"
                                               name="clients[]"
                                               value="{{ $client->id }}"{{ (in_array($client->id, $currentClients) ? ' checked' : '') }}> {{ $client->name }}
                                    </div>
                                @endforeach
                            @endif
                        @endif
                    </div>
                    <div class="col-md-6">
                        <h3>Image Management</h3>
                        <p>Modpack art (logo, icon and background images) are handled entirely by the
                            <a href="https://www.technicpack.net/" target="_blank" rel="noopener">Technic Platform</a>.
                        </p>
                        <p>To change your modpack art you should go to your modpack settings in Platform and choose the
                            "Resources" section.</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <hr>
                        <input type="submit" class="btn btn-success" value="Save Modpack">
                        <a href="{{ url('/modpack/delete/'.$modpack->id) }}" class="btn btn-danger">Delete Modpack</a>
                        <a href="{{ url()->previous() }}" class="btn btn-primary">Go Back</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script type="text/javascript">
        $("#slug").slugify('#name');
    </script>
@endsection
