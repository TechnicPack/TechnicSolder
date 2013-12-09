@layout('layouts/main')
@section('content')
<h1>Client Management</h1>
<hr>
<h2>Add a New Client</h2>
@if ($errors->all())
    <div class="alert alert-error">
    @foreach ($errors->all() as $error)
        {{ $error }}<br />
    @endforeach
    </div>
@endif
{{ Form::horizontal_open() }}
{{ Form::hidden("add-client", 1) }}
{{ Form::control_group(Form::label('name', 'Name'), Form::xxlarge_text('name')) }}
{{ Form::control_group(Form::label('uuid', 'UUID'), Form::xxlarge_text('uuid')) }}
{{ Form::actions(array(Button::primary_submit('Add Client'),Button::link(URL::to('client/list'),'Go Back'))) }}
{{ Form::close() }}
@endsection