@extends('meta.meta')

@section('body')

@section('navbar')

  <div class="btn-group position-tr z-top cursor-pointer">
    
    <!-- dropdown menu icon -->
    <span class="dropdown-icon fa fa-2x fa-cog fa-inverse color-hovered drop-shadow" alt="Settings" title="Settings" data-toggle="dropdown" aria-expanded="true"></span>

    <!-- dropdown menu elements -->
    <ul class="dropdown-menu pull-right" role="menu">
      <li>
        <a target="_blank" href="https://github.com/tryfruit/fruit-dashboard/">
          <span class="fa fa-puzzle-piece"></span> About
        </a>
      </li>
      <li>
        <a onClick= '_gaq.push(["_trackEvent", "Sign in", "Button Pushed"]);mixpanel.track("Signout");' href="{{ URL::route('auth.signin') }}">
          <span class="fa fa-sign-in"></span> Sign in
        </a>
      </li>
    </ul>

  </div> <!-- /.btn-group -->

@show

@section ('pageAlert')
@include('meta.pageAlerts')
@show

@section('pageContent')
@show

@section('footer')
@include('meta.footer')
@show

</body>

@stop