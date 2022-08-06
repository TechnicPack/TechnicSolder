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
                        <b>{{ property_exists($mod, "displayName") ? $mod->displayName : $mod->name }}</b>
                        <br/>
                        {{ property_exists($mod, "displaySummary") ? $mod->displaySummary : $mod->summary }}
                    </td>
                    <td>{{ $mod->authors }}</td>
                    <td>{!! Html::link($mod->websiteUrl, $mod->websiteUrl, ["target" => "_blank"]) !!}</td>
                    <td>{!! Html::link('mod/import/details/'.$provider.'/'.$mod->id,'Import', ["class" => "btn btn-xs btn-primary"]) !!}</td>
                </tr>
            @endforeach
        </table>
        <div class="text-center">
            <a class="btn btn-default" {!! $pagination->currentPage <= 1 ? "href=\"#\" disabled" : "href=\"?page=" . strval($pagination->currentPage - 1) . "\"" !!}><i class="fa fa-angle-left fa-fw"></i></a> Page {{ number_format($pagination->currentPage) }} of {{ number_format($pagination->totalPages) }} ({{ number_format($pagination->totalItems) }}) <a class="btn btn-default" {!! $pagination->currentPage >= $pagination->totalPages ? "href=\"#\" disabled" : "href=\"?page=" . strval($pagination->currentPage + 1) . "\"" !!}><i class="fa fa-angle-right fa-fw"></i></a>
        </div>
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