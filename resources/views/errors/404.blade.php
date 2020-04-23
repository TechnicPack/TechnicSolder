<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Not Found - Technic Solder</title>

    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link href="{{ URL::to('css/errors.css') }}" rel="stylesheet" type='text/css'>
    <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
    <link href='//fonts.googleapis.com/css?family=Raleway:400,300,100|Open+Sans:400,700' rel='stylesheet' type='text/css'>
</head>

<body>
<div class="container">
    <h1>Page not found!</h1>
    <p style="text-align: center">The page you're looking for doesn't exist.</p>
    <p class="links"><a href="{{ URL::previous() }}" target="_blank"><i class="fa fa-arrow-circle-left"></i> Take me back to where I was!</a> <a href="https://github.com/TechnicPack/TechnicSolder/issues" target="_blank"><i class="fa fa-life-ring"></i> I need help!</a></p>
    <p class="meta"><span class="label"><i class="fa fa-fire"></i></span> 404 <span class="label"><i class="fa fa-cog"></i></span> Page not found</p>
</div>
</body>
</html>