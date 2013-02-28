@layout('layouts/modpack')
@section('content')
<h1>Modpack Management</h1>
<hr>
<h2>{{ $modpack->name }}</h2>
<hr>
<button class="btn btn-primary pull-right" href="{{ URL::to('modpack/build/create') }}">Create New Build</button>
{{ Table::open() }}
{{ Table::headers('#', 'Build Number', 'Mod Count', 'Created', '') }}
@foreach ($modpack->builds as $build)
	<tr>
		<td>{{ $build->id }}</td>
		<td>{{ $build->version }}</td>
		<td>{{ count($build->modversions) }}</td>
		<td>{{ $build->created_at }}</td>
		<td>{{ HTML::link('modpack/build/'.$build->id, "Manage") }}</td>
	</tr>
@endforeach
{{ Table::close() }}
@endsection