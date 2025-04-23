@extends('layouts.master')
@section('title')
    <title>{{ $build->version }} &ndash; {{ $build->modpack->name }} &ndash; Technic Solder</title>
@stop
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
                <a href="{{ url('modpack/build/' . $build->id) }}" class="btn btn-xs btn-primary">Add mods</a>
                <a href="{{ url('modpack/view/' . $build->modpack->id) }}" class="btn btn-xs btn-default">Back to
                    modpack</a>
            </div>
        </div>
        <div class="panel-body">
            @if ($build->is_published)
            <div class="alert alert-warning">If changes are made, users will need to re-install the modpack
                if they have already installed this build.
            </div>
            @endif
            @include('partial.form-errors')
            <form action="{{ url()->full() }}" method="post" accept-charset="UTF-8">
                @csrf
                <input type="hidden" name="confirm-edit" value="1">
                <div class="row">
                    <div class="col-md-6">
                        <h4>Edit Build</h4>
                        <p>Here you can modify the properties of existing builds.</p>
                        <hr>
                        <div class="form-group">
                            <label for="version">Build name</label>
                            <input type="text"
                                   class="form-control"
                                   name="version"
                                   id="version"
                                   value="{{ old('version', $build->version) }}"
                            >
                        </div>
                        <div class="form-group">
                            <label for="version">Minecraft version</label>
                            <select class="form-control" name="minecraft">
                                @foreach ($minecraft as $version)
                                    <option value="{{ $version['version'] }}"
                                            @selected($build->minecraft == $version['version'])
                                    >{{ $version['version'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h4>Build Requirements</h4>
                        <p>These requirements are passed to the launcher and prevent players without the required minimum settings from playing your modpack</p>
                        <hr>
                        <div class="form-group">
                            <label for="java-version">Required Java version (at least)</label>
                            <select class="form-control" name="java-version" id="java-version">
                                <option value="17" @selected($build->min_java === '17')>Java 17</option>
                                <option value="16" @selected($build->min_java === '16')>Java 16</option>
                                <option value="1.8" @selected($build->min_java === '1.8')>Java 1.8</option>
                                <option value="1.7" @selected($build->min_java === '1.7')>Java 1.7</option>
                                <option value="1.6" @selected($build->min_java === '1.6')>Java 1.6</option>
                                <option value="" @selected(empty($build->min_java))>No Requirement</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="memory">Required RAM/memory (in <abbr title="megabytes (or mebibytes)">MB</abbr>)</label>
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <input type="checkbox"
                                           id="memory-enabled"
                                           name="memory-enabled"
                                           aria-label="mb"
                                           @checked($build->min_memory)
                                    >
                                </span>
                                <input type="number"
                                       class="form-control"
                                       name="memory"
                                       id="memory"
                                       aria-label="mb"
                                       aria-describedby="addon-mb"
                                       value="{{ $build->min_memory ?: '' }}"
                                       @disabled($build->min_memory)
                                >
                                <span class="input-group-addon" id="addon-mb">MB</span>
                            </div>
                            <p class="help-block">Check the checkbox to enable the memory requirement.</p>
                        </div>
                    </div>
                </div>
                <hr>
                <input type="submit" class="btn btn-success" value="Save changes">
                <a href="{{ url('/modpack/build/'.$build->id) }}" class="btn btn-primary">Go back</a>
                <a href="{{ url('/modpack/build/'.$build->id, ['action' => 'delete']) }}" class="btn btn-danger pull-right">Delete build</a>
            </form>
        </div>
    </div>
@endsection
@section('bottom')
    <script type="text/javascript">
        const memoryEnabledField = $('#memory-enabled');
        memoryEnabledField.change(function () {
            const memoryAmountField = $('#memory');

            if ($(this).is(':checked')) {
                memoryAmountField.val(memoryAmountField.data('value') || '').prop('disabled', false);
            } else {
                memoryAmountField.data('value', memoryAmountField.val());
                memoryAmountField.val('').prop('disabled', true);
            }
        });
    </script>
@endsection
