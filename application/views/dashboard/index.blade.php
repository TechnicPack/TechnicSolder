@layout('layouts/main')
@section('content')
<h1>Dashboard</h1>
@if (Session::has('permission'))
<div class="alert alert-error">
	{{ Session::get('permission') }}
</div>
@endif
<p>Welcome to TechnicSolder</p>
@endsection