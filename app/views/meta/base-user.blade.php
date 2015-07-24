@extends('meta.meta')

@section('body')

  <body style="background: url({{ Background::dailyBackgroundURL() }}) no-repeat center center fixed">   
    @section('navbar')
      @include('meta.navbar')
    @show

    @section('pageContent')

    @show

    @section('footer')
      @include('meta.footer')
    @show
  </body>

@stop