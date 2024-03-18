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
    <form class="vertical-form" method="post" action="{{ route('password.update') }}">
      <div style="margin:0;padding:0;display:inline;">
        <legend>
        Technic Solder
        </legend>
        @if (Session::has('errors'))
          <ul class="notice errors">
            <li>{{ $errors->first('email') }}</li>
            <li>{{ $errors->first('password') }}</li>
            <li>{{ $errors->first('password_confirmation') }}</li>
          </ul>
        @endif
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <input type="text" class="input-block-level" name="email" placeholder="Email Address" required autofocus>
        <input type="password" class="input-block-level" name="password" placeholder="Password" required autofocus>
        <input type="password" class="input-block-level" name="password_confirmation" placeholder="Confirm Password" required autofocus>
        <input name="login" type="submit" value="Reset Password">
        <div class="footer">
          <p><a href="https://www.technicpack.net/">Powered by the Technic Platform</a></p>
        </div>
      </div>
    </form>
  </body>
</html>
