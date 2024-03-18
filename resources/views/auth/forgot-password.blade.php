<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Sign in &middot; Technic Solder</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link href='//fonts.googleapis.com/css?family=Open+Sans:400,600,700' rel='stylesheet' type='text/css'>
    <!-- Le styles -->
    {!! Html::style('css/login.css') !!}
  </head>
  <body class="login">
    <img alt="Technic-logo" class="logo" height="70" src="{{ URL::asset('img/wrenchIcon.svg') }}">
    <form class="vertical-form" method="post" action="">
      <div style="margin:0;padding:0;display:inline;">
        <legend>
        Technic Solder
        </legend>
        @if (Session::has('error'))
          <ul class="notice errors">
            <li>{{ Session::get('error') }}</li>
          </ul>
        @endif
        @if (Session::has('success'))
          <ul class="notice success">
            <li>{{ Session::get('success') }}</li>
          </ul>
        @endif
        <input type="text" name="email" class="input-block-level" placeholder="Email Address" size="30" autocomplete="off">
        <input name="login" type="submit" value="Reset password">
        <div class="footer">
          <p><a href="https://www.technicpack.net/">Powered by the Technic Platform</a></p>
        </div>
      </div>
    </form>
  </body>
</html>
