@layout('layouts/master')
@section('content')
<h1>Client Management</h1>
<hr>
<div class="panel panel-default">
	<div class="panel-heading">
	Client List
	</div>
	<div class="panel-body">
		<p>This is the client management area. Here you can register your launcher client UUID to Solder so that private builds will show up to you in the launcher. After a client is added to this list, they need to be linked to the modpacks you want them to have access to in Solder.</p>
		@if (Session::has('success'))
			<div class="alert alert-success">
				{{ Session::get('success') }}
			</div>
		@endif
		<div class="pull-right">
		    <a href="{{ URL::to('client/create') }}" class="btn btn-success"><i class="icon-plus icon-white"></i> Add Client</a>
		</div>
		{{ Table::open() }}
		{{ Table::headers('#', 'Name', 'UUID', '') }}
		@foreach ($clients as $client)
			<tr>
				<td>{{ $client->id }}</td>
				<td>{{ $client->name }}</td>
				<td>{{ $client->uuid }}</td>
				<td>{{ HTML::link('client/delete/'.$client->id, 'Delete') }}</td>
			</tr>
		@endforeach
		{{ Table::close() }}
		@endsection
	</div>
</div>