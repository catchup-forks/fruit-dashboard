@extends('meta.meta')

@section('body')

  <body @if(Auth::user()->settings->background_enabled) style="background: url({{Background::dailyBackgroundURL()}}) no-repeat center center fixed" @endif>
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