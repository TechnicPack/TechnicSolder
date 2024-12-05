@extends('layouts.master')
@section('title')
    <title>Mod Library - Technic Solder</title>
@stop
@section('content')
    <div class="page-header">
        <h1>Mod Library</h1>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="pull-right">
                <a href="{{ URL::to('mod/create') }}" class="btn btn-xs btn-success">Add Mod</a>
            </div>
            Mod List
        </div>
        <div class="panel-body">
            @if (Session::has('success'))
                <div class="alert alert-success">
                    {{ Session::get('success') }}
                </div>
            @endif
            @if ($errors->all())
                <div class="alert alert-danger">
                    @foreach ($errors->all() as $error)
                        {{ $error }}<br/>
                    @endforeach
                </div>
            @endif
            <table class="table table-striped table-bordered table-hover" id="dataTables">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Mod Name</th>
                    <th>Author</th>
                    <th>Website</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($mods as $mod)
                    <tr>
                        <td><a href="{{ url('/mod/view/'.$mod->id) }}">{{ $mod->id }}</a></td>
                        <td>
                            <a href="{{ url('/mod/view/'.$mod->id) }}">{{ $mod->pretty_name ?: $mod->name }}</a>
                            @if (!empty($mod->pretty_name))
                                ({{ $mod->name }})
                            @endif
                            <br>
                            <b>Latest version:</b> {{ $mod->latestVersion?->version ?? "N/A" }}
                        </td>
                        <td>{{ !empty($mod->author) ? $mod->author : "N/A" }}</td>
                        <td>
                            @if (!empty($mod->link))
                                <a href="{{ $mod->link }}"
                                   target="_blank"
                                   rel="noopener noreferrer"
                                >{{ $mod->link }}</a>
                            @else
                                N/A
                            @endif
                        </td>
                        <td><a href="{{ url('/mod/view/'.$mod->id) }}" class="btn btn-primary btn-xs">Manage</a></td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
@endsection
@section('bottom')
    <script type="text/javascript">
        $(document).ready(function () {
            $('#dataTables').dataTable({
                "order": [[1, "asc"]]
            });

        });
    </script>
@endsection
