<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>TechnicSolder v{{ SOLDER_VERSION }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    {{ Asset::container('bootstrapper')->styles() }}
    {{ Asset::container('bootstrapper')->scripts() }}
    {{ Asset::styles() }}
    {{ Asset::scripts() }}
    <style type="text/css">
      body {
        padding-top: 60px;
        padding-bottom: 40px;
      }
      .sidebar-nav {
        padding: 9px 0;
      }

      @media (max-width: 980px) {
        /* Enable use of floated navbar text */
        .navbar-text.pull-right {
          float: none;
          padding-left: 5px;
          padding-right: 5px;
        }
      }
    </style>
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,600,700' rel='stylesheet' type='text/css'>
  </head>

  <body>

    <div class="navbar navbar-inverse navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container-fluid">
          <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="brand" href="{{ URL::to('dashboard') }}">TechnicSolder</a>
          <div class="nav-collapse collapse">
            <p class="navbar-text pull-right">
              Logged in as <a href="#" class="navbar-link">{{ Auth::user()->email }}</a>. ({{ HTML::link('logout','Logout') }})
            </p>
            <ul class="nav">
              <li><a href="{{ URL::to('dashboard') }}">Dashboard</a></li>
              <li class="active"><a href="{{ URL::to('modpack') }}">Modpacks</a></li>
              <li><a href="{{ URL::to('mod/list') }}">Mod Library</a></li>
            </ul>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>
    <div class="container-fluid">
      <div class="row-fluid">
        <div class="span3">
          <div class="well sidebar-nav">
            <ul class="nav nav-list">
              <li class="nav-header">Current Modpacks</li>
              @foreach (Modpack::all() as $modpack)
                <li{{ $active = (URI::is('modpack/view/'.$modpack->id) ? ' class="active"' : null) }}><a href="{{ URL::to('modpack/view/'.$modpack->id) }}"><img src="{{ Config::get('solder.mirror_url').$modpack->slug.'/resources/icon.png' }}" style="width: 16px; height: 16px;"> {{ $modpack->name }}{{ $hidden = ($modpack->hidden ? " (Hidden)" : "") }}</a></li>
              @endforeach
              <li{{ $active = (URI::is('modpack/create') ? ' class="active"' : null) }}><a href="{{ URL::to('modpack/create') }}"><i class="icon-plus"></i> Create New Modpack</a></li>
            </ul>
          </div><!--/.well -->
        </div><!--/span-->
        <div class="span9">
          @__yield('content')
        </div><!--/span-->
      </div><!--/row-->

      <hr>

      <footer>
        <p>TechnicSolder v{{ SOLDER_VERSION }}-{{ SOLDER_STREAM }}</p>
        <p style="font-size: smaller">TechnicSolder is an open source project. It is under the MIT license. Feel free to do whatever you want!</p>
      </footer>

    </div><!--/.fluid-container-->

  </body>
</html>
