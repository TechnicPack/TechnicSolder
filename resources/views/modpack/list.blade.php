@extends('layouts.master')
@section('title')
    <title>Modpack Management - Technic Solder</title>
@stop
@section('content')
    <h1>Modpack Management</h1>
    <hr>

    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="pull-right">
                <a href="{{ URL::to('modpack/create') }}"
                   class="btn btn-success btn-xs"
                ><i class="icon-plus icon-white"></i>Create Modpack</a>
            </div>
            Modpack List
        </div>
        <div class="panel-body">
            @if (Session::has('success'))
                <div class="alert alert-success">
                    {{ Session::get('success') }}
                </div>
            @endif
            @include('partial.form-errors')
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover" id="dataTables">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Slug</th>
                        <th>Rec</th>
                        <th>Latest</th>
                        <th>Hidden</th>
                        <th>Private</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($modpacks as $modpack)
                        <tr>
                            <td>{{ $modpack->name }}</td>
                            <td>{{ $modpack->slug }}</td>
                            <td>{{ $modpack->recommended ?: "N/A" }}</td>
                            <td>{{ $modpack->latest ?: "N/A" }}</td>
                            <td>{{ $modpack->hidden ? "Yes" : "No" }}</td>
                            <td>{{ $modpack->private ? "Yes" : "No" }}</td>
                            <td>
                                <a href="{{ url('/modpack/view/'.$modpack->id) }}" class="btn btn-warning btn-xs">Manage
                                    Builds</a>
                                <a href="{{ url('/modpack/edit/'.$modpack->id) }}"
                                   class="btn btn-primary btn-xs"
                                >Edit</a>
                                <a href="{{ url('/modpack/delete/'.$modpack->id) }}" class="btn btn-danger btn-xs">Delete</a>
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
        $(document).ready(function () {
            $('#dataTables').dataTable();
        });
    </script>
@endsection