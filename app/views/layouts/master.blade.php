<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    @section('title')
      <title>TechnicSolder {{ SOLDER_VERSION }}</title>
    @show
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{{ asset('favicon.ico') }}}">
    <script src="{{{ asset('js/jquery-1.11.1.min.js') }}}"></script>
    <script src="{{{ asset('js/bootstrap.min.js') }}}"></script>
    <script src="{{{ asset('js/jquery.jgrowl.min.js') }}}"></script>
    <link href="{{{ asset('css/bootstrap.min.css') }}}" rel="stylesheet">
    <link href="{{{ asset('font-awesome/css/font-awesome.css') }}}" rel="stylesheet">
    <link href="{{{ asset('css/sb-admin.css') }}}" rel="stylesheet">
    <link href="{{{ asset('css/solder.css') }}}" rel="stylesheet">
    <script src="{{{ asset('js/plugins/metisMenu/jquery.metisMenu.js') }}}"></script>
    <script src="{{{ asset('js/sb-admin.js') }}}"></script>
    <script src="{{{ asset('js/plugins/dataTables/jquery.dataTables.js') }}}"></script>
    <script src="{{{ asset('js/plugins/dataTables/dataTables.bootstrap.js') }}}"></script>
    <link href="{{{ asset('css/dataTables.bootstrap.css') }}}" rel="stylesheet">
    <script src="{{{ asset('js/jquery.slugify.js') }}}"></script>
    <script src="{{{ asset('js/nav-float.js') }}}"></script>
    <link href="{{{ asset('css/OpenSansfont.css') }}}" rel="stylesheet">
    @yield('top')
  </head>
  <body>
    <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".sidebar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="{{ URL::to('dashboard') }}"><img src="{{ URL::asset('img/title.png') }}"> {{ SOLDER_VERSION }}</a>
        </div>
        <ul class="nav navbar-top-links navbar-left">
          @if (Cache::get('update'))
          <li>
              <a href="{{ URL::to('solder/update') }}" style="color:orangered;">
                 Update Available! <i class="fa fa-exclamation-triangle"></i>
              </a>
          </li>
          @endif
        </ul>
        <ul class="nav navbar-top-links navbar-right">
            <li><a href="http://docs.solder.io/" target="blank_">Help <i class="fa fa-question"></i></a></li>
            <li class="dropdown">
                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                  {{ Auth::user()->username }} <i class="fa fa-user fa-fw"></i>  <i class="fa fa-caret-down"></i>
                </a>
                <ul class="dropdown-menu dropdown-user">
                    <li><a href="{{ URL::to('user/edit/'.Auth::user()->id) }}"><i class="fa fa-user fa-fw"></i> User Profile</a>
                    </li>
                    <li><a href="{{ URL::to('logout') }}"><i class="fa fa-sign-out fa-fw"></i> Logout</a>
                    </li>
                </ul>
                <!-- /.dropdown-user -->
            </li>
            <!-- /.dropdown -->
        </ul>
        <!-- /.navbar-top-links -->

    </nav>
    <!-- /.navbar-static-top -->


    <nav class="navbar-default navbar-static-side" role="navigation">
      <div class="sidebar-collapse">
          <ul class="nav side-menu" id="side-menu">
              <li>
                  <a href="{{ URL::to('dashboard') }}" {{ Request::is('dashboard') ? ' class="active"' : '' }}><i class="fa fa-dashboard fa-fw"></i> Dashboard</a>
              </li>
              <li>
                  <a href="#"><i class="fa fa-folder fa-fw"></i> Modpacks<span class="fa arrow"></span></a>
                  <ul class="nav nav-second-level">

                       @foreach (Modpack::all() as $modpack)
                        @if ($modpack->icon)
                          @if (Config::get('solder.use_s3'))
                          <li><a href="{{ URL::to('modpack/view/'.$modpack->id) }}"><img src="{{ Config::get('solder.s3_url') }}resources/{{ $modpack->slug }}/icon.png" style="width: 16px; height: 16px;"> {{ $modpack->name }}{{ $hidden = ($modpack->hidden ? " (Hidden)" : "") }}</a></li>
                          @else
                          <li><a href="{{ URL::to('modpack/view/'.$modpack->id) }}"><img src="{{ URL::asset('resources/' . $modpack->slug . '/icon.png') }}" style="width: 16px; height: 16px;"> {{ $modpack->name }}{{ $hidden = ($modpack->hidden ? " (Hidden)" : "") }}</a></li>
                          @endif
                        @else
                          <li><a href="{{ URL::to('modpack/view/'.$modpack->id) }}"><img src="{{ URL::asset('resources/default/icon.png') }}" style="width: 16px; height: 16px;"> {{ $modpack->name }}{{ $hidden = ($modpack->hidden ? " (Hidden)" : "") }}</a></li>
                        @endif
                      @endforeach
                      <li><a href="{{ URL::to('modpack/list') }}">Modpack List</a></li>
                      <li><a href="{{ URL::to('modpack/create') }}">Add Modpack</a></li>
                  </ul>
                  <!-- /.nav-second-level -->
              </li>
              <li>
                  <a href="#"><i class="fa fa-book fa-fw"></i> Mod Library<span class="fa arrow"></span></a></a>
                  <ul class="nav nav-second-level">
                       <li><a href="{{ URL::to('mod/list') }}">Mod List</a></li>
                       <li><a href="{{ URL::to('mod/create') }}">Add a Mod</a></li>
                  </ul>
              </li>
              <li>
                  <a href="#"><i class="fa fa-wrench fa-fw"></i> Configure Solder<span class="fa arrow"></span></a>
                  <ul class="nav nav-second-level">
                      <li>
                          <a href="{{ URL::to('solder/configure') }}">Main Settings</a>
                      </li>
                      <li>
                          <a href="{{ URL::to('solder/update') }}">Update Checker</a>
                      </li>
                      <li>
                          <a href="{{ URL::to('user/list') }}">User Management</a>
                      </li>
                      <li>
                          <a href="{{ URL::to('client/list') }}">Client Management</a>
                      </li>
                      <li><a href="{{ URL::to('key/list') }}">API Key Management</a></li>
                  </ul>
                  <!-- /.nav-second-level -->
              </li>
          </ul>
          <!-- /#side-menu -->
      </div>
      <!-- /.sidebar-collapse -->
  </nav>
  <!-- /.navbar-static-side -->

  <div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
        @yield('content')
        </div>
    </div>
    <!-- /.row -->
</div>
<!-- /#page-wrapper -->
<script type="text/javascript">
    (function($){
        $(function(){
            $.jGrowl.defaults.closerTemplate = '<div class="alert alert-info">Close All</div>';
        });
    })(jQuery);
    </script>
@yield('bottom')
  </body>
</html>
