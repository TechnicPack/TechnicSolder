@layout('layouts/main')
@section('content')
<h1>User Management</h1>
<hr>
<h2>User List</h2>
{{ Table::open() }}
{{ Table::headers('#', 'Email', 'Username', 'Created', '') }}
@foreach ($users as $user)
	<tr>
		<td>{{ $user->id }}</td>
		<td>{{ $user->email }}</td>
		<td>{{ $user->username }}</td>
		<td>{{ $user->created_at }}</td>
		<td>{{ HTML::link('user/edit/'.$user->id,'Edit') }} - {{ HTML::link('user/delete/'.$user->id, 'Delete') }}</td>
	</tr>
@endforeach
{{ Table::close() }}
@endsection