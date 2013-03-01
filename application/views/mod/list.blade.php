@layout('layouts/mod')
@section('navigation')
@parent
<li class="nav-header">Mod: None Selected</li>
@endsection
@section('content')
<h1>Mod Library</h1>
<hr>
<div>
	<form class="form-search" method="get" action="{{ URL::current() }}">
	  <input type="text" name="search" value="{{ $search }}" class="input-medium search-query">
	  <button type="submit" class="btn">Search</button>
	</form>
	{{ $mods->appends(array('search' => $search))->links(3,null, Paginator::SIZE_SMALL) }}
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
{{ $mods->appends(array('search' => $search))->links(3,null, Paginator::SIZE_SMALL) }}
@endsection