@extends('layouts/master')
@section('title')
    <title>Import Mod - Technic Solder</title>
    <style> 
    /* This is done due to not using Bootstrap 4+ */
    .row-flex {
        display: -webkit-box;
        display: -webkit-flex;
        display: -ms-flexbox;
        display: flex;
        flex-wrap: wrap;
        }
    .row-flex > [class*='col-'] {
        display: flex;
        flex-direction: column;
    }
    </style>
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
        <div class="row row-flex">
            <div class="col-xs-1" style='padding-right:0px;justify-content:center;'>
                {!! !empty($mod->thumbnailUrl) ? Html::image($mod->thumbnailUrl, $mod->thumbnailDesc, array('style' => 'width:100%;')) : "" !!}
            </div>
            <div class="col-xs-11">
                <p><b>Name:</b> {{ property_exists($mod, "displayName") ? $mod->displayName : $mod->name }}</p>
                <p><b>Summary:</b> {{ property_exists($mod, "displaySummary") ? $mod->displaySummary : $mod->summary }}</p>
                <p><b>Author(s):</b> {{ $mod->authors }}</p>
                <p><b>Link:</b> {!! Html::link($mod->websiteUrl, $mod->websiteUrl, ["target" => "_blank"]) !!}</p>
            </div>
        </div>
        <br>
        <form method="post">
            <table class="table table-striped table-bordered table-hover" id="dataTables">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Filename</th>
                        <th>MC Versions</th>
                    </tr>
                </thead>
                <tbody>
                @foreach ($mod->versions as $versionName => $version)
                    <tr>
                        <td style='text-align:center;vertical-align:middle;'><input type="checkbox" name="{{ base64_encode($versionName) }}"></td>
                        <td>{{ $versionName }}</td>
                        <td>{{ $version->filename }}</td>
                        <td>{{ implode(', ', $version->gameVersions) }}</td>
                    </tr>
                @endforeach
            </table>
            {!! Form::submit('Import', ['class' => 'btn btn-success']) !!}
            {!! Html::link(URL::previous(), 'Go Back', ['class' => 'btn btn-primary']) !!}
        </form>
        {{-- <div class="text-center">
            <a class="btn btn-default" {!! $pagination->currentPage <= 1 ? "href=\"#\" disabled" : "href=\"?page=" . strval($pagination->currentPage - 1) . "\"" !!}><i class="fa fa-angle-left fa-fw"></i></a> Page {{ number_format($pagination->currentPage) }} of {{ number_format($pagination->totalPages) }} ({{ number_format($pagination->totalItems) }}) <a class="btn btn-default" {!! $pagination->currentPage >= $pagination->totalPages ? "href=\"#\" disabled" : "href=\"?page=" . strval($pagination->currentPage + 1) . "\"" !!}><i class="fa fa-angle-right fa-fw"></i></a>
        </div> --}}
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