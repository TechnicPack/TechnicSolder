@layout('layouts/modpack')
@section('content')
<h1>Modpack Management</h1>
<hr>
<h2>Delete Modpack: {{ $modpack->name }}</h2>
<p>Deleting a modpack is irreversible. All associated builds will be immediately removed. This will remove them from your API. Users with this modpack already on their launcher will be able to continue to use it in "Offline Mode."</p>
{{ Form::open() }}
{{ Form::actions(array(Button::danger_submit('Confirm Deletion'))) }}
{{ Form::close() }}
@endsection