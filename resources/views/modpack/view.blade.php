@extends('layouts.master')
@section('title')
    <title>{{ $modpack->name }} - Technic Solder</title>
@stop
@section('content')
    <h1>Build Management - {{ $modpack->name }}</h1>
    <hr>
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="pull-right">
                <a class="btn btn-primary btn-xs" href="{{ URL::to('modpack/add-build/'.$modpack->id) }}">Create New
                    Build</a>
                <a class="btn btn-warning btn-xs" href="{{ URL::to('modpack/edit/'.$modpack->id) }}">Edit Modpack</a>
            </div>
            Build Management: {{ $modpack->name }}
        </div>
        <div class="panel-body">
            @session('success')
                <div class="alert alert-success">
                    {{ $value }}
                </div>
            @endsession
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover" id="dataTables">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Build Number</th>
                        <th>MC Version</th>
                        <th>Mod Count</th>
                        <th>Rec</th>
                        <th>Latest</th>
                        <th>Published</th>
                        <th>Private</th>
                        <th>Created on</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($modpack->builds->sortByDesc('id') as $build)
                        <tr>
                            <td>{{ $build->id }}</td>
                            <td>{{ $build->version }}</td>
                            <td>{{ $build->minecraft }}</td>
                            <td>{{ $build->modversions_count }}</td>
                            <td>
                                <input autocomplete="off"
                                       type="radio"
                                       name="recommended"
                                       value="{{ $build->version }}"
                                       @checked($modpack->recommended === $build->version)
                                >
                            </td>
                            <td>
                                <input autocomplete="off"
                                       type="radio"
                                       name="latest"
                                       value="{{ $build->version }}"
                                       @checked($modpack->latest === $build->version)
                                >
                            </td>
                            <td>
                                <input autocomplete="off"
                                       type="checkbox"
                                       name="published"
                                       class="published"
                                       rel="{{ $build->id }}"
                                       @checked($build->is_published)
                                >
                            </td>
                            <td>
                                <input autocomplete="off"
                                       type="checkbox"
                                       name="private"
                                       class="private"
                                       rel="{{ $build->id }}"
                                       @checked($build->private)
                                >
                            </td>
                            <td>{{ $build->created_at }}</td>
                            <td>
                                <a href="{{ url('/modpack/build/'.$build->id) }}"
                                   class="btn btn-primary btn-xs"
                                >Manage</a>
                                <a href="{{ url()->query('/modpack/build/'.$build->id, ['action' => 'edit']) }}"
                                   class="btn btn-warning btn-xs"
                                >Edit</a>
                                <a href="{{ url()->query('/modpack/build/'.$build->id, ['action' => 'delete']) }}"
                                   class="btn btn-danger btn-xs"
                                >Delete</a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
@section('bottom')
    <script type="text/javascript">
        $("input[name=recommended]").change(function () {
            $.ajax({
                type: "POST",
                url: "{{ URL::to('modpack/modify/recommended?modpack='.$modpack->id) }}&recommended=" + encodeURIComponent($(this).val()),
                success: function (data) {
                    $.jGrowl(data.success, {group: 'alert-success'});
                },
                error: function (xhr, textStatus, errorThrown) {
                    $.jGrowl(errorThrown || `Unknown error: ${textStatus}`, {group: 'alert-danger'});
                }
            });
        });

        $("input[name=latest]").change(function () {
            $.ajax({
                type: "POST",
                url: "{{ URL::to('modpack/modify/latest?modpack='.$modpack->id) }}&latest=" + encodeURIComponent($(this).val()),
                success: function (data) {
                    $.jGrowl(data.success, {group: 'alert-success'});
                },
                error: function (xhr, textStatus, errorThrown) {
                    $.jGrowl(errorThrown || `Unknown error: ${textStatus}`, {group: 'alert-danger'});
                }
            });
        });

        $(".published").change(function () {
            var checked = 0;
            if (this.checked)
                checked = 1;
            $.ajax({
                type: "POST",
                url: "{{ URL::to('modpack/modify/published') }}?build=" + $(this).attr("rel") + "&published=" + checked,
                success: function (data) {
                    $.jGrowl(data.success, {group: 'alert-success'});
                },
                error: function (xhr, textStatus, errorThrown) {
                    $.jGrowl(errorThrown || `Unknown error: ${textStatus}`, {group: 'alert-danger'});
                }
            })
        });

        $(".private").change(function () {
            var checked = 0;
            if (this.checked)
                checked = 1;
            $.ajax({
                type: "POST",
                url: "{{ URL::to('modpack/modify/private') }}?build=" + $(this).attr("rel") + "&private=" + checked,
                success: function (data) {
                    $.jGrowl(data.success, {group: 'alert-success'});
                },
                error: function (xhr, textStatus, errorThrown) {
                    $.jGrowl(errorThrown || `Unknown error: ${textStatus}`, {group: 'alert-danger'});
                }
            })
        });

        $(document).ready(function () {
            $('#dataTables').dataTable({
                "order": [[0, "desc"]]
            });
        });
    </script>
@endsection
