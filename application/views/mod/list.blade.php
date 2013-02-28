@layout('layouts/mod')
@section('navigation')
@parent
<li class="nav-header">Mod: None Selected</li>
@endsection
@section('content')
<h1>Mod Library</h1>
<hr>
<div style="text-align:center">
	{{ $mods->links() }}
</div>
{{ Table::open() }}
{{ Table::headers('#','Mod Name', 'Author', '') }}
@foreach ($mods->results as $mod)
	<tr>
		<td>{{ $mod->id }}</td>
		@if (!empty($mod->pretty_name))
			<td>{{ $mod->pretty_name }} ({{ $mod->name }})</td>
		@else
			<td>{{ $mod->name }}</td>
		@endif
		<td>{{ $mod->author }}
		<td>{{ HTML::link('mod/view/'.$mod->id,'Manage') }}</td>
	</tr>
@endforeach
{{ Table::close() }}
<div style="text-align:center">
	{{ $mods->links() }}
</div>
@endsection