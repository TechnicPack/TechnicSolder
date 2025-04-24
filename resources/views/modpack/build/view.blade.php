@extends('layouts.master')
@section('title')
    <title>{{ $build->version }} - {{ $build->modpack->name }} - Technic Solder</title>
@stop
@push('top')
    <script src="{{ asset('js/selectize.min.js') }}"></script>
    <link href="{{ asset('css/selectize.bootstrap3.css') }}" rel="stylesheet">
@endpush
@section('content')
    <div class="page-header">
        <h1>Build Management</h1>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading clearfix">
            <h3 class="panel-title pull-left" style="padding-top: 0.3rem;padding-bottom: 0.3rem">
                {{ $build->modpack->name }} &mdash; build {{ $build->version }}
            </h3>
            <div class="pull-right">
                <button onclick="window.location.reload()" class="btn btn-xs btn-success">Refresh</button>
                <a href="{{ URL::to('modpack/build/' . $build->id . '?action=edit') }}" class="btn btn-xs btn-primary">Edit</a>
                <a href="{{ URL::to('modpack/view/' . $build->modpack->id) }}" class="btn btn-xs btn-default">Back to
                    modpack</a>
            </div>
        </div>
        <div class="panel-body">
            <div class="row row-no-gutters">
                <div class="col-lg-3 col-md-6">
                    <p><b>Build:</b> {{ $build->version }}</p>
                </div>
                <div class="col-lg-3 col-md-6">
                    <p><b>Minecraft version:</b> {{ $build->minecraft }}</p>
                </div>
                <div class="col-lg-3 col-md-6">
                    <p><b>Required Java version:</b> {{ $build->min_java ?: 'Not set'  }}</p>
                </div>
                <div class="col-lg-3 col-md-6">
                    <p><b>Required RAM/memory:</b> {{ $build->min_memory ? $build->min_memory . ' MB' : 'Not set' }}</p>
                </div>
            </div>
        </div>
    </div>
@if ($build->isLive())
    <div class="alert alert-warning">
        <p>This build is currently published and not marked as private. <strong>You are editing a live build</strong>.<br>
        <br>
        Build management panels have been hidden. <a style="cursor:pointer" onclick="document.getElementById('edit-panel').classList.remove('hidden');return false">Click here to show them</a>.</p>
    </div>
@endif
    <div id="edit-panel" @class(['hidden' => $build->isLive()])>
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th style="width: 60%">Add a mod</th>
                            <th></th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <form method="post"
                              action="{{ url('/modpack/build/modify') }}"
                              accept-charset="UTF-8"
                              class="mod-add"
                              autocomplete="off"
                              class="form-horizontal"
                        >
                            @csrf
                            <input type="hidden" name="build" value="{{ $build->id }}">
                            <input type="hidden" name="action" value="add">
                            <tr id="mod-list-add">
                                <td>
                                    <i class="icon-plus"></i>
                                    <select class="form-control" name="mod-name" id="mod" placeholder="Select a mod...">
                                        <option value=""></option>
                                        @foreach ($mods as $mod)
                                            <option value="{{ $mod->name }}">{{ $mod->pretty_name ?: $mod->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <select class="form-control"
                                            name="mod-version"
                                            id="mod-version"
                                            placeholder="Select a mod version..."
                                    >
                                    </select>
                                </td>
                                <td>
                                    <input type="submit" class="btn btn-success btn-small" value="Add to build">
                                </td>
                            </tr>
                        </form>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table" id="mod-list">
                        <thead>
                        <tr>
                            <th id="mod-header" style="width: 60%">Mod Name</th>
                            <th>Version</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($build->modversions->sortByDesc('build_id', SORT_NATURAL) as $ver)
                            <tr>
                                <td>
                                    <a href="{{ url('/mod/view/'.$ver->mod->id) }}">{{ $ver->mod->pretty_name ?: $ver->mod->name }}</a><br>
                                    ({{ $ver->mod->name }})
                                </td>
                                <td>
                                    <form method="post"
                                          action="{{ url('/modpack/build/modify') }}"
                                          accept-charset="UTF-8"
                                          style="margin-bottom: 0"
                                          class="mod-version"
                                          autocomplete="off"
                                    >
                                        @csrf
                                        <input type="hidden" class="build-id" name="build_id" value="{{ $build->id }}">
                                        <input type="hidden"
                                               class="modversion-id"
                                               name="modversion_id"
                                               value="{{ $ver->pivot->modversion_id }}"
                                        >
                                        <input type="hidden" name="action" value="version">
                                        <div class="form-group input-group">
                                            <select class="form-control" name="version">
                                                @foreach ($ver->mod->versions as $version)
                                                    <option value="{{ $version->id }}"
                                                            @selected($ver->version == $version->version)
                                                    >{{ $version->version }}</option>
                                                @endforeach
                                            </select>
                                            <span class="input-group-btn">
                                                <input type="submit" class="btn btn-primary" value="Change">
                                            </span>
                                        </div>
                                    </form>
                                </td>
                                <td>
                                    <form method="post"
                                          action="{{ url('/modpack/build/modify') }}"
                                          accept-charset="UTF-8"
                                          style="margin-bottom: 0"
                                          class="mod-delete"
                                    >
                                        @csrf
                                        <input type="hidden" name="build_id" value="{{ $build->id }}">
                                        <input type="hidden"
                                               class="modversion-id"
                                               name="modversion_id"
                                               value="{{ $ver->pivot->modversion_id }}"
                                        >
                                        <input type="hidden" name="action" value="delete">
                                        <input type="submit" class="btn btn-danger btn-small" value="Remove">
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('bottom')
    <script>
        var $select = $("#mod").selectize({
            dropdownParent: "body",
            create: false,
            maxItems: 1,
            sortField: {
                field: 'text',
                direction: 'asc'
            },
            searchField: ['text', 'value'],
            onChange: function (value) {
                if (!value.length) return;

                modversion.disable();

                // First clear the current selection and then clear all the options, otherwise
                // the previously selected item will remain in the available options
                modversion.clear();
                modversion.clearOptions();

                modversion.load(function (callback) {
                    $.ajax({
                        type: "GET",
                        url: "{{ URL::to('mod/versions') }}/" + mod.getValue(),
                        success: function (data) {
                            if (data.versions.length === 0) {
                                $.jGrowl("No Modversions found for " + data.pretty_name, {group: 'alert-warning'});
                                $("#mod-version").attr("placeholder", "No Modversions found...");
                                callback();
                            } else {
                                callback(data.versions.map(function (x) {
                                    return {value: x, text: x}
                                }));
                                modversion.enable();
                                modversion.refreshOptions(true);
                                $("#mod-version").attr("placeholder", "Select a Modversion...");
                            }
                        },
                        error: function (xhr, textStatus, errorThrown) {
                            $.jGrowl(textStatus + ': ' + errorThrown, {group: 'alert-danger'});
                            callback();
                        }
                    });
                });
            }
        });
        var mod = $select[0].selectize;
        var $select = $("#mod-version").selectize({
            dropdownParent: "body",
            create: false,
            maxItems: 1,
            sortField: {
                field: 'text',
                direction: 'asc'
            },
        });
        var modversion = $select[0].selectize;

        $(".mod-version").submit(function (e) {
            e.preventDefault();
            $.ajax({
                type: "POST",
                url: "{{ URL::to('modpack/modify/version') }}",
                data: $(this).serialize(),
                success: function (data) {
                    console.log(data.reason);
                    if (data.status == 'success') {
                        $.jGrowl("Modversion Updated", {group: 'alert-success'});
                    } else if (data.status == 'failed') {
                        $.jGrowl("Unable to update modversion", {group: 'alert-warning'});
                    } else if (data.status == 'aborted') {
                        $.jGrowl("Mod was already set to that version", {group: 'alert-success'});
                    }
                },
                error: function (xhr, textStatus, errorThrown) {
                    $.jGrowl(textStatus + ': ' + errorThrown, {group: 'alert-danger'});
                }
            });
        });

        $(".mod-delete").submit(function (e) {
            e.preventDefault();
            $.ajax({
                type: "POST",
                url: "{{ URL::to('modpack/modify/delete') }}",
                data: $(this).serialize(),
                success: function (data) {
                    console.log(data.reason);
                    if (data.status == 'success') {
                        $.jGrowl("Modversion Deleted", {group: 'alert-success'});
                    } else {
                        $.jGrowl("Unable to delete modversion", {group: 'alert-warning'});
                    }
                },
                error: function (xhr, textStatus, errorThrown) {
                    $.jGrowl(textStatus + ': ' + errorThrown, {group: 'alert-danger'});
                }
            });
            $(this).parent().parent().fadeOut();
        });

        $(".mod-add").submit(function (e) {
            e.preventDefault();
            if ($("#mod-version").val()) {
                $.ajax({
                    type: "POST",
                    url: "{{ URL::to('modpack/modify/add') }}",
                    data: $(this).serialize(),
                    success: function (data) {
                        if (data.status == 'success') {
                            $("#mod-list-add").after('<tr><td>' + data.pretty_name + '</td><td>' + data.version + '</td><td></td></tr>');
                            $.jGrowl("Mod " + data.pretty_name + " added at " + data.version, {group: 'alert-success'});
                        } else {
                            $.jGrowl("Unable to add mod. Reason: " + data.reason, {group: 'alert-warning'});
                        }
                    },
                    error: function (xhr, textStatus, errorThrown) {
                        $.jGrowl(textStatus + ': ' + errorThrown, {group: 'alert-danger'});
                    }
                });
            } else {
                $.jGrowl("Please select a mod version", {group: 'alert-warning'});
            }
        });

        $(document).ready(function () {
            $("#mod-list").dataTable({
                "order": [[0, "asc"]],
                "autoWidth": false,
                "columnDefs": [
                    {"width": "60%", "targets": 0},
                    {"width": "30%", "targets": 1}
                ]
            });

            // Start with the mod versions dropdown disabled by default, since the user
            // has to select a mod first, and then it updates all the versions
            modversion.disable();
        });
    </script>
@endsection
