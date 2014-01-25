@layout('layouts/master')
@section('content')
<h1>API Key Management</h1>
<hr>
<h2>Delete API Key ({{ $key->name }})</h2>
<p>This will immediately remove access to Solder using this API Key. Make sure to unlink any packs using this key before doing this.</p>
<form method="post" action="{{ URL::current() }}">
	<button type="submit" class="btn btn-danger">Confirm Deletion</button> 
	<a href="{{ Request::referrer() }}" class="btn">Go Back</a>
</form>
@endsection