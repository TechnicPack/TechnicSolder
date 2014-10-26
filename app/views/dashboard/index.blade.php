@extends('layouts/master')
@section('content')
<div class="page-header">
<h1>Solder Dashboard</h1>
<p class="lead">Welcome to Technic Solder!</p>
</div>
@if (Session::has('permission'))
<div class="alert alert-danger">
	{{ Session::get('permission') }}
</div>
@endif
<p>TechnicSolder v{{ SOLDER_VERSION }}-{{ SOLDER_STREAM }}</p>
<p>TechnicSolder is an open source project. It is under the MIT license. Source Code: <a href="http://github.com/TechnicPack/TechnicSolder" target="_blank">http://github.com/TechnicPack/TechnicSolder</a></p>
@endsection