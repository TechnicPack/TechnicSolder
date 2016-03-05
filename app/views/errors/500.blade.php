<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Application Error - Technic Solder</title>

    <link rel="shortcut icon" href="{{{ asset('favicon.ico') }}}">
    <link href="{{ URL::to('css/errors.css') }}" rel="stylesheet" type='text/css'>
    <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
    <link href='//fonts.googleapis.com/css?family=Raleway:400,300,100|Open+Sans:400,700' rel='stylesheet' type='text/css'>
  </head>

  <body>
    <div class="container">
      <h1>Look's like we've hit a snag!</h1>
      <p>Something went terribly wrong when we were fetching the page you requested. Please try your request again in a few minutes or follow the help link to find your issue.</p>
      <p class="links"><a href="{{ URL::previous() }}" target="_blank"><i class="fa fa-arrow-circle-left"></i> Take me back to where I was!</a> <a href="https://github.com/TechnicPack/TechnicSolder/issues" target="_blank"><i class="fa fa-life-ring"></i> I need help!</a></p>
      <p class="meta"><span class="label"><i class="fa fa-fire"></i></span> {{ $code }} <span class="label"><i class="fa fa-cog"></i></span> {{ $exception->getMessage() }}</p>
    </div>
  </body>
</html>