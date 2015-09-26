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
    <link href="{{{ asset('css/style.min.css') }}}" rel="stylesheet">
<!--     <link href="{{{ asset('font-awesome/css/font-awesome.css') }}}" rel="stylesheet"> -->
<!--     <link href="{{{ asset('css/sb-admin.css') }}}" rel="stylesheet"> -->
<!--     <link href="{{{ asset('css/solder.css') }}}" rel="stylesheet"> -->
<!--     <script src="{{{ asset('js/plugins/metisMenu/jquery.metisMenu.js') }}}"></script> -->
<!--     <script src="{{{ asset('js/sb-admin.js') }}}"></script> -->
    <script src="{{{ asset('js/plugins/dataTables/jquery.dataTables.js') }}}"></script>
    <script src="{{{ asset('js/plugins/dataTables/dataTables.bootstrap.js') }}}"></script>
<!--     <link href="{{{ asset('css/dataTables.bootstrap.css') }}}" rel="stylesheet"> -->
    <script src="{{{ asset('js/jquery.slugify.js') }}}"></script>
<!--     <script src="{{{ asset('js/nav-float.js') }}}"></script> -->
<!--     <link href="{{{ asset('css/OpenSansfont.css') }}}" rel="stylesheet"> -->
    @yield('top')
    <script src="{{{ asset('js/menu/modernizr.js') }}}"></script>
    <script>
      $(document).ready(function() {

      $(".fa-eye-slash .link").tooltip({
          animation:'false',
          placement: 'left',
          title: 'Hidden',
        });

        $(".fa-lock .link").tooltip({
          animation:'false',
          placement: 'left',
          title: 'Private',
        });


        $('[data-toggle="tooltip"]').tooltip();
        $(".mp-level").click(function() {
          if ($(".mp-level-open").hasClass("mp-level-active")) {
            setTimeout ( function() { $(".mp-level-active").addClass("scroller");}, 500 );
    //      alert('amazing things');

            $("#filter-packs").keyup(function(){
                // Retrieve the input field text and reset the count to zero
                var filter = $(this).val(), count = 0;

                // Loop through the comment list
                $(".ul-searchable.ul-modpacks li").each(function(){

                    // If the list item does not contain the text phrase fade it out
                    if ($(this).text().search(new RegExp(filter, "i")) < 0) {
                        $(this).hide();

                    // Show the list item if the phrase matches and increase the count by 1
                    } else {
                        $(this).show();
                        count++;
                    }
                });

                // Update the count
            });

            $("#filter-mods").keyup(function(){
              // Retrieve the input field text and reset the count to zero
              var filter = $(this).val(), count = 0;

              // Loop through the comment list
              $(".ul-searchable.ul-mods li").each(function(){
                  // If the list item does not contain the text phrase fade it out
                  if ($(this).text().search(new RegExp(filter, "i")) < 0) {
                      $(this).hide();

                  // Show the list item if the phrase matches and increase the count by 1
                  } else {
                      $(this).show();
                      count++;
                  }
              });

              // Update the count
            });

          }
        });
      });

      $(window).resize(function() {
          setTimeout ( function() { if($(".mp-level").width() == 280){

           location.reload();
          return;
          }
          }, 1000 );
      });
    </script>
  </head>
  <body>
    <div class="containers">
      <div class="mp-pusher" id="mp-pusher">
        <nav id="mp-menu" class="mp-menu">
          <div class="mp-level">
            <h2 class="icon-fa fa-home"><img class="img-responsive" src="https://in.kato.im/7a429330b18875f49a12f41b5093919cfb8d02afbac0dbeb3b464e62b93e64/solder-1.svg"></h2>
            <div class="col-xs-3">
              <img class="img-responsive" src="https://crafatar.com/avatars/{{ Auth::user()->username }}">
            </div>
            <div class="col-xs-9">
              <a class="link lead">{{ Auth::user()->username }}</a>
              <p>{{ Auth::user()->email }}</p>
            </div>
            <hr style="border-color: #24313E;border-width: thick;">
            <br>
            <div class="alert alert-info" role="alert" style="border-radius: 0;margin-bottom: 0;">
              <strong>Note</strong> You are using a pre-release version of Solder, this update only includes an updated Menu, most things have been tested, exercise caution.
            </div>
            <ul class="side-nav ">
              <li class="icon-fa fa-dashboard">
                <a class="link " href="{{ URL::to('dashboard') }}">Dashboard</a>
              </li>
              <!-- MODPACKS -->
              <li class="icon icon-arrow-left">
                <a class="link " href="#">Modpacks</a>
                <div class="mp-level mp-level-modpacks de-scroller">
                  <h2 class="icon icon-display">Modpacks</h2>
                  <a class="link mp-back" href="#">back</a>
                  <div style="bottom:0;" class="btn-group btn-group-justified" role="group" aria-label="...">
                    <a href="{{ URL::to('modpack/list') }}" class="btn btn-info"><i class="fa fa-list"></i> List</a>
                    <a href="{{ URL::to('modpack/create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> Create</a>
                  </div>
                  <style>
                    .form-search {
                      color: white;
                      background-color: #202B36;
                      border: 1px solid #2D3D4D;
                    }
                  </style>
                  <input style="border-radius: 0;" type="text" class="form-control form-search" id="filter-packs" placeholder="Search for a Modpack">
                  <ul class="hover ul-searchable ul-modpacks scroller" id="modpacks">
                    @foreach (Modpack::all() as $modpack)
                    @if ($modpack->private)
                    <li class="icon-fa fa-lock">
                    @elseif ($modpack->hidden)
                    <li class="icon-fa fa-eye-slash">
                    @else
                    <li class="icon icon-arrow-left">
                    @endif
                      <a style="white-space: nowrap;" class="icon link" href="{{ URL::to('modpack/view/'.$modpack->id) }}"><img height="16" width="16"  src="{{ $icon = ($modpack->icon ? URL::asset('resources/' . $modpack->slug . '/icon.png') : URL::asset('resources/default/icon.png')) }}"> {{ $modpack->name }}</a>
                    </li>
                    @endforeach
                  </ul>
                </div>
              </li>
              <!-- MODS -->
              <li class="icon icon-arrow-left">
                <a class="link " href="#">Mods Library</a>
                <div class="mp-level mp-level-modpacks de-scroller">
                  <h2 class="icon icon-display">Mods</h2>
                  <a class="link mp-back" href="#">back</a>
                    <div style="bottom:0;" class="btn-group btn-group-justified" role="group" aria-label="...">
                      <a href="{{ URL::to('mod/list') }}" class="btn btn-info"><i class="fa fa-list"></i> List</a>
                      <a href="{{ URL::to('mod/create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> Mod</a>
                    </div>
                  <input style="border-radius: 0;" type="text" class="form-control form-search" id="filter-mods" placeholder="Search for a Mod">
                  <ul class="hover ul-searchable scroller ul-mods" id="mods">
                    @foreach (Mod::all() as $mod)
                    <li class='icon icon-arrow-left'>
                      <a style='white-space: nowrap' class='icon link' href="{{ URL::to('mod/view/'.$mod->id) }}">
                      {{ $mod = (!empty($mod->pretty_name) ? ($mod->pretty_name) : ($mod->name)) }}
                      </a>
                    </li>
                    @endforeach
                  </ul>
                </div>
              </li>
              <!-- CONFIG -->
              <li class="icon icon-arrow-left">
                <a class="link icon" href="#">Configuration</a>
                <div class="mp-level" style="">
                  <h2 class="icon">Configuration</h2>
                  <a class="link mp-back" href="#">back</a>
                  <ul>
                    <li class="icon-fa fa-cog">
                      <a class="link" href="{{ URL::to('solder/configure') }}">Settings</a>
                    </li>
                    <li class="icon-fa fa-check-square-o">
                      <a class="link" href="{{ URL::to('solder/update') }}">Update Checker</a>
                    </li>
                    <li class="icon-fa fa-user">
                      <a class="link" href="{{ URL::to('user/list') }}">Manage Users</a>
                    </li>
                    <li class="icon-fa fa-desktop">
                      <a class="link" href="{{ URL::to('client/list') }}">Manage Clients</a>
                    </li>
                    <li class="icon-fa fa-cloud-upload">
                      <a class="link" href="{{ URL::to('key/list') }}">Platform API Keys</a>
                    </li>
                  </ul>
                </div>
              </li>
            </ul>
            <div style="bottom:0;z-index: -100;" class="moggle btn-group btn-group-justified" role="group" aria-label="...">
              <a href="http://docs.solder.io/" target="_blank" class="btn btn-primary btn-sm btn-bottom"><i class="fa fa-info-circle"></i> Help</a>
              <a href="{{ URL::to('user/edit/'.Auth::user()->id) }}" class="btn btn-info btn-sm btn-bottom"><i class="fa fa-pencil"></i> Edit Profile</a>
              <a href="{{ URL::to('logout') }}" class="btn btn-danger btn-sm btn-bottom"><i class="fa fa-sign-out"></i> Log Out</a>
            </div>
          </div>
        </nav>
        <a id="menu-toggle" href="#" class="btn btn-dark btn-lg toggle toggle-pos"><i class="fa fa-bars"></i></a>
        <div id="page-wrapper" class="">
          <div class="row" class="">
            <div class="col-lg-12">
              @yield('content')
            </div>
          </div>
        </div>
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
<script src="{{{ asset('js/menu/classie.js') }}}"></script>
<script src="{{{ asset('js/menu/mlpushmenu.js') }}}"></script>
<script>
  new mlPushMenu( document.getElementById( 'mp-menu' ), document.getElementById( 'menu-toggle' ) );

  function resize()
  {
    var heights = window.innerHeight;
    var widths = window.innerHeight;
    document.getElementById("modpacks").style.height = heights - 189 + "px";
    document.getElementById("mods").style.height = heights - 189 + "px";
  }

  resize();

  window.onresize = function() {
    resize();
  };
</script>
  </body>
</html>
