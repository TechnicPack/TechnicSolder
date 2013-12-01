@layout('layouts/main')
@section('content')
<h1>User Management</h1>
<hr>
<h2>Delete User ({{ $user->username }})</h2>
<form method="post" action="{{ URL::current() }}">
	<button type="submit" class="btn btn-danger">Confirm Deletion</button> 
	<a href="{{ Request::referrer() }}" class="btn">Go Back</a>
</form>
@endsection