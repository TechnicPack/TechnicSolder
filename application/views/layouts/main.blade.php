@layout('layouts/master')
@section('layout')
  <div class="navbar navbar-inverse navbar-static-top" role="navigation">
    <div class="container">
      <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="{{ URL::to('dashboard') }}">TechnicSolder</a>
      </div>
      <div class="collapse navbar-collapse">
        
        <ul class="nav navbar-nav">
          <li class="active"><a href="{{ URL::to('dashboard') }}">Dashboard</a></li>
          <li><a href="{{ URL::to('modpack') }}">Modpacks</a></li>
          <li><a href="{{ URL::to('mod/list') }}">Mod Library</a></li>
        </ul>
        <ul class="nav navbar-nav navbar-right">
          <li><a href="#" class="navbar-link">Logged in as {{ Auth::user()->username }}</a></li>
          <li>{{ HTML::link('logout','Logout') }}</li>
        </ul>
      </div><!--/.nav-collapse -->
    </div>
  </div>
    <div class="container">
      <div class="row">
        <div class="col-md-3">
            <ul class="nav nav-pills nav-stacked">
              <li class="nav-header">Solder</li>
              <li{{ $active = (URI::is('dashboard') ? ' class="active"' : null) }}><a href="{{ URL::to('dashboard') }}"><i class="icon-home"></i> Dashboard</a></li>
              <li{{ $active = (URI::is('user/edit/'.Auth::user()->id) ? ' class="active"' : null) }}><a href="{{ URL::to('user/edit/'.Auth::user()->id) }}"><i class="icon-edit"></i> Edit My Account</a></li>
              <li class="nav-header">Manage Solder</li>
              <li{{ $active = (URI::is('solder/configure') ? ' class="active"' : null) }}><a href="{{ URL::to('solder/configure') }}"><i class="icon-cog"></i> Configuration</a></li>
              <li{{ $active = (URI::is('user/*') && !URI::is('user/edit/'.Auth::user()->id) ? ' class="active"' : null) }}><a href="{{ URL::to('user/list') }}"><i class="icon-user"></i> Manage Users</a></li>
              <li{{ $active = (URI::is('client/*') ? ' class="active"' : null) }}><a href="{{ URL::to('client/list') }}"><i class="icon-hdd"></i> Manage Clients</a></li>
            </ul>
        </div><!--/span-->
        <div class="col-md-9">
          @yield('content')
        </div><!--/span-->
      </div><!--/row-->
@endsection