@extends('layouts/master')
@section('title')
    <title>Tags - TechnicSolder</title>
@stop
@section('content')
    <div class="page-header">
        <h1>Tags</h1>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="pull-right">
                <a href="{{ URL::to('tag/create') }}" class="btn btn-xs btn-success">Add Tag</a>
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
                        {{ $error }}<br />
                    @endforeach
                </div>
            @endif
            <table class="table table-striped table-bordered table-hover" id="dataTables">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tag Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tags as $tag)
                        <tr>
                            <td>{{ HTML::link('tag/view/'.$tag->id, $tag->id) }}</td>
                            <td>
                                @if (!empty($tag->pretty_name))
                                    {{ HTML::link('tag/view/'.$tag->id, $tag->pretty_name) }} ({{ $tag->name }})
                                @else
                                    {{ HTML::link('tag/view/'.$tag->id, $tag->name) }}
                                @endif
                            </td>
                            <td>{{ HTML::link('tag/view/'.$tag->id,'Manage', array("class" => "btn btn-xs btn-primary")) }}</td>
                        </tr>
                @endforeach
            </table>
        </div>
    </div>
@endsection
@section('bottom')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#dataTables').dataTable({
                "order": [[ 1, "asc" ]]
            });

        });
    </script>
@endsection
