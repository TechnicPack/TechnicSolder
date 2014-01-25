@layout('layouts/master')
@section('content')
<div class="page-header">
<h1>User Management</h1>
</div>
<div class="panel panel-default">
    <div class="panel-heading">
    Delete User ({{ $user->username }})
    </div>
    <div class="panel-body">
		<form method="post" action="{{ URL::current() }}">
			<button type="submit" class="btn btn-danger">Confirm Deletion</button> 
			<a href="{{ Request::referrer() }}" class="btn">Go Back</a>
		</form>
	</div>
</div>
@endsection