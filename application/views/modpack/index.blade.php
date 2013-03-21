@layout('layouts/modpack');
@section('content')
<h1>Modpack Management</h1>
<hr>
@if (Session::has('deleted'))
<div class="alert alert-error">
	{{ Session::get('deleted') }}
</div>
@endif
<p>Select the modpack you wish to manage on the left.</p>
@endsection