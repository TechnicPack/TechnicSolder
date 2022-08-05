@extends('layouts/master')
@section('title')
    <title>Import Mod - Technic Solder</title>
@stop
@section('content')
<div class="page-header">
<h1>Mod Library</h1>
</div>
<div class="panel panel-default">
    <div class="panel-heading">
    Import Mod
    </div>
    <div class="panel-body">
        @if ($errors->all())
            <div class="alert alert-danger">
            @foreach ($errors->all() as $error)
                {{ $error }}<br />
            @endforeach
            </div>
        @endif
        <form class="form-inline" method="GET" id="searchForm" style="display:flex;flex-direction:row;">
            <div class="form-group" style="flex-grow:1;margin-right:2px;">
                <label class="sr-only" for="inputSearch">Search</label>
                <input type="text" class="form-control" id="inputSearch" placeholder="Mod name" style="width:100%;" value="{{ $query }}">
            </div>
            <div class="form-group" style="margin-right:2px;">
                <label class="sr-only" for="inputProvider">Provider</label>
                <select class="form-control" id="inputProvider">
                    @foreach ($providers as $providerKey => $tmpProvider)
                    <option value="{{ $providerKey }}" {{ $provider == $providerKey ? "selected" : "" }}>{{ $tmpProvider::name() }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Search</button>
        </form>
        <br>
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
                    <td style='text-align:center;vertical-align:middle;'>{!! !empty($mod->thumbnailUrl) ? Html::image($mod->thumbnailUrl, $mod->thumbnailDesc, array('style' => 'height:32px')) : "" !!}</td>
                    <td>
                        <b>{{ $mod->name }}</b>
                        <br/>
                        {{ $mod->summary }}
                    </td>
                    <td>{{ $mod->authors }}</td>
                    <td>{!! Html::link($mod->websiteUrl, $mod->websiteUrl, ["target" => "_blank"]) !!}</td>
                    <td>
                        <form method="post" action="{{ URL::to('mod/import') }}">
                            <input type="hidden" name="provider" id="provider" value="{{ $provider }}">
                            <input type="hidden" name="modid" id="modid" value="{{ $mod->id }}">
                            <input type="submit" value="Import" class="btn btn-xs btn-primary">
                        </form>
                    </td>
                </tr>
            @endforeach
        </table>
        Page {{ $pagination->currentPage }} of {{ $pagination->totalPages }} ({{ $pagination->totalItems }})
    </div>
</div>
@endsection
@section('bottom')
<script type="text/javascript">
$("#searchForm").submit(function(event) {
    event.preventDefault();
    window.location.href = "{{ URL::to('mod/import') }}/" + $("#inputProvider").val() + "/" + $("#inputSearch").val()
});
</script>
@endsection