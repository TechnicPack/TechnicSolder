@layout('layouts/mod')
@section('navigation')
@parent
<li class="nav-header">Mod: None Selected</li>
@endsection
@section('content')
<h1>Mod Library</h1>
<hr>
@if (Session::has('deleted'))
<div class="alert alert-error">
	{{ Session::get('deleted') }}
</div>
@endif
<div>
	<form class="form-search" method="get" action="{{ URL::current() }}">
	  <input type="text" name="search" value="{{ $search }}" class="input-medium search-query">
	  <button type="submit" class="btn">Search</button>
	</form>
	{{ $mods->appends(array('search' => $search))->links(3,null, Paginator::SIZE_SMALL) }}
</div>
{{ Table::open() }}
{{ Table::headers('#','Mod Name', 'Author', 'Option','URL') }}
@foreach ($mods->results as $mod)
	<tr>
		<td>{{ HTML::link('mod/view/'.$mod->id, $mod->id) }}</td>
		@if (!empty($mod->pretty_name))
			<td>{{ $mod->pretty_name }} ({{ $mod->name }})</td>
		@else
			<td>{{ $mod->name }}</td>
		@endif
		<td>{{ $mod->author }}
		<td>{{ HTML::link('mod/view/'.$mod->id,'Manage') }}</td>
    @if (!empty($mod->link))
    <td>{{ HTML::link($mod->link,__('mod.website'), array('target'=>'blank')) }}</td>
    @else
		<td>No URL</td>
    @endif
	</tr>
@endforeach
{{ Table::close() }}
{{ $mods->appends(array('search' => $search))->links(3,null, Paginator::SIZE_SMALL) }}
@endsection