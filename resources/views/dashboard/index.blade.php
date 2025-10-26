@extends('layouts.master')
@section('title')
    <title>Dashboard - Technic Solder</title>
@stop
@section('content')
    <div class="page-header">
        <h1>Solder Dashboard</h1>
        <p class="lead">Welcome to Technic Solder!</p>
    </div>
    @session('permission')
        <div class="alert alert-danger">
            {{ $value }}
        </div>
    @endsession
    <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
        <div class="panel panel-default">
            <div class="panel-heading" role="tab" id="recentModpacksHeading">
                <h3 class="panel-title">
                    <a data-toggle="collapse"
                       data-parent="#accordion"
                       href="#recentModpacks"
                       aria-expanded="true"
                       aria-controls="recentModpacks"
                    >
                        <i class="fa fa-refresh"></i> Recently Updated Modpacks
                    </a>
                </h3>
            </div>
            <div id="recentModpacks"
                 class="panel-collapse collapse in"
                 role="tabpanel"
                 aria-labelledby="recentModpacksHeading"
            >
                <div class="panel-body">
                    <table class="table table-striped table-bordered table-hover" id="dataTables">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Build #</th>
                            <th>Modpack</th>
                            <th>MC Version</th>
                            <th># of Mods</th>
                            <th>Updated on</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($builds as $build)
                            <tr>
                                <td>{{ $build->id }}</td>
                                <td>{{ $build->version }}</td>
                                <td>{{ $build->modpack->name }}
                                <td>{{ $build->minecraft }}</td>
                                <td>{{ $build->modversions_count }}</td>
                                <td>{{ $build->updated_at }}</td>
                                <td><a href="{{ url('/modpack/build/'.$build->id) }}" class="btn btn-warning btn-xs">Manage
                                        Build</a></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading" role="tab" id="recentModVersionsHeading">
                <h3 class="panel-title">
                    <a data-toggle="collapse"
                       data-parent="#accordion"
                       href="#recentModVersions"
                       aria-expanded="true"
                       aria-controls="recentModVersions"
                    >
                        <i class="fa fa-refresh"></i> Recently Added Mod Versions
                    </a>
                </h3>
            </div>
            <div id="recentModVersions"
                 class="panel-collapse collapse in"
                 role="tabpanel"
                 aria-labelledby="recentModVersionsHeading"
            >
                <div class="panel-body">
                    <table class="table table-striped table-bordered table-hover" id="dataTables">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Version #</th>
                            <th>Mod Name</th>
                            <th>Author</th>
                            <th>Website</th>
                            <th>Created On</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($modversions as $modversion)
                            <tr>
                                <td>
                                    <a href="{{ url('/mod/view/'.$modversion->mod->id) }}">{{ $modversion->mod->id }}</a>
                                </td>
                                <td>{{ $modversion->version }}</td>
                                <td>
                                    <a href="{{ url('/mod/view/'.$modversion->mod->id) }}">
                                        {{ $modversion->mod->pretty_name ?: $modversion->mod->name }}
                                    </a>
                                    @if (!empty($modversion->mod->pretty_name))
                                        ({{ $modversion->mod->name }})
                                    @endif
                                </td>
                                <td>{{ $modversion->mod->author ?: "N/A" }}</td>
                                <td>
                                    @if (empty($modversion->mod->link))
                                        N/A
                                    @else
                                        <a href="{{ url($modversion->mod->link) }}"
                                           target="_blank"
                                           rel="noopener noreferrer"
                                        >{{ $modversion->mod->link }}</a>
                                    @endif
                                </td>
                                <td>{{ $modversion->created_at }}
                                <td><a href="{{ url('/mod/view/'.$modversion->mod->id.'#versions') }}"
                                       class="btn btn-primary btn-xs"
                                    >Manage</a></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading" role="tab" id="changelogHeading">
                <h3 class="panel-title">
                    <a data-toggle="collapse"
                       data-parent="#accordion"
                       href="#changelog"
                       aria-expanded="true"
                       aria-controls="changelog"
                    >
                        <i class="fa fa-list"></i> Changelog
                    </a>
                </h3>
            </div>
            <div id="changelog" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="changelogHeading">
                <div class="panel-body">
                    <p><strong>You're running Solder v{{SOLDER_VERSION}}</strong></p>
                    @if (array_key_exists('error',$changelog))
                        <div class="alert alert-warning">{{ $changelog['error'] }}</div>
                    @else
                        <ul>
                            @foreach ($changelog as $change)
                                <li><code><a href="{{ url($change['html_url']) }}"
                                             rel="noopener noreferrer"
                                        >{{ substr($change['sha'], 0, 7) }}</a></code>
                                    <span style="margin-left:5px;margin-right:5px;">
                                        <i class="fa fa-angle-double-left fa-1"></i>
                                    </span> {{ explode("\n", $change['commit']['message'], 2)[0] }} </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <p><a href="https://github.com/TechnicPack/TechnicSolder" target="_blank" rel="noopener noreferrer">Technic
            Solder</a> is an open source project, under the MIT license.</p>
@endsection
