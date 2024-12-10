@extends('layouts.master')
@section('title')
    <title>API Key Management - Technic Solder</title>
@stop
@section('content')
    <h1>API Key Management</h1>
    <hr>
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="pull-right">
                <a href="{{ URL::to('key/create') }}"
                   class="btn btn-success btn-xs"
                ><i class="icon-plus icon-white"></i> Add API Key</a>
            </div>
            API Key List
        </div>
        <div class="panel-body">
            <p>This is the list of API keys that have access to Solder.</p>
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
                        <th>Name</th>
                        <th>API Key</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($keys as $key)
                        <tr>
                            <td>{{ $key->id }}</td>
                            <td>{{ $key->name }}</td>
                            <td>{{ $key->api_key }}</td>
                            <td><a href="{{ url('/key/delete/'.$key->id) }}" class="btn btn-danger btn-xs">Delete</a>
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