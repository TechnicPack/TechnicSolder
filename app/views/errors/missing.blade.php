@extends('layouts/master')
@section('top')
    {{ HTML::style('css/errors.css')}}
@endsection
@section('content')
<div class="container">
      <h1>404 - Page not found!</h1>
      <p style="margin:0 0 10px 145px;">The page you are looking for does not exist!</p>
    </div>
@endsection
