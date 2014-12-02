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
<div class="panel panel-default">
	<div class="panel-heading">
	<h4>Changelog</h4>
	</div>
	<div class="panel-body">
		<p><strong>0.7-DEV</strong></p>
		<ul>
  		<li>Upgraded Laravel Framework from 3.0 to 4.2.x</li>
  		<li>New login page</li>
  		<li>Updated User Permission System
  		<ul>
  			<li>Added Manage API/Clients</li>
  			<li>Added Global Modpack permissions <em>(These are required before assigning specific modpack access)</em>
  			<ul>
  				<li>Create</li>
  				<li>Manage</li>
  				<li>Delete</li>
  			</ul>
  			</li>
  		</ul>
  		</li>
  		<li>Mod Library sort by name on default</li>
  		<li>Improved Mod Version error messages
  		<ul>
  			<li>MD5 Hashing failure/success</li>
  			<li>Adding new version failure/success</li>
  			<li>Deleting a version failure/success</li>
  		</ul>
  		</li>
  		<li>New Modpack Management page</li>
  		<li>Optimize Build Management
  		<ul>
  			<li>Sort/Search mods when adding</li>
  			<li>Builds views now sort by mod name by default</li>
  			<li>Added ability to search for mods within builds</li>
  			<li>Builds views are now paginated</li>
  		</ul>
  		</li>
  		<li>More frequent updates!</li>
		</ul>
	</div>
</div>
<p>TechnicSolder v{{ SOLDER_VERSION }}-{{ SOLDER_STREAM }}</p>
<p>TechnicSolder is an open source project. It is under the MIT license. Source Code: <a href="http://github.com/TechnicPack/TechnicSolder" target="_blank">http://github.com/TechnicPack/TechnicSolder</a></p>
@endsection