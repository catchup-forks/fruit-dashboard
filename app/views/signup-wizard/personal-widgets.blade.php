@extends('meta.base-user')

  @section('pageTitle')
    Personal widgets
  @stop

  @section('pageStylesheet')
  @stop

  @section('pageContent') 
    @if(Auth::check())
        YES
    @else
        NO
    @endif
    
    sadfasdf
  @stop

  @section('pageScripts')
  @stop
