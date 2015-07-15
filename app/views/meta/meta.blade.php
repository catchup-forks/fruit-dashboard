<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="chrome-webstore-item" href="https://chrome.google.com/webstore/detail/cgmdkfkbilmbclifhmfgabbkkcfjcicp">
    <link rel="shortcut icon" href="{{ asset('images/favicon.png') }}">

    <title>
      Fruit Dashboard
      @if (trim($__env->yieldContent('pageTitle')))
        | @yield('pageTitle')
      @endif
    </title>

    @section('stylesheet')
      <!-- Fonts -->
      <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,400italic,700,800' rel='stylesheet' type='text/css'>
      <link href='https://fonts.googleapis.com/css?family=Open+Sans+Condensed:300,700' rel='stylesheet' type='text/css'>
      <!-- /Fonts -->

      <!-- Bootstrap CSS -->
      {{ HTML::style('css/bootstrap.min.css') }}
      <!-- /Bootstrap CSS-->

      <!-- Font Awesome CSS -->
      {{ HTML::style('css/font-awesome.min.css') }}
      <!-- /FontAwesome CSS-->

      <!-- Gridster CSS -->
      {{ HTML::style('css/jquery.gridster.min.css') }}
      <!-- /Gridster CSS-->

      <!-- Growl CSS -->
      {{ HTML::style('css/jquery.growl.css') }}
      <!-- /Growl CSS-->

      <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
      <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <![endif]-->

      <!-- Custom styles -->
      {{ HTML::style('css/custom.css') }}
      <!-- /Custom styles -->
      
      <!-- Google Analytics -->
      @include('meta.google-analytics')
      <!-- Google Analytics -->

      <!-- Mixpanel event -->
      {{ HTML::script('js/mixpanel_events.js') }}
      <!-- / Mixpanel event -->

      <!-- Mixpanel user tracking -->
      {{ HTML::script('js/mixpanel_users.js') }}
      <!-- / Mixpanel user tracking -->

      <!-- Page specific stylesheet -->
      @section('pageStylesheet')
      @show
      <!-- /Page specific stylesheet -->
    @show
  </head>

  
  @section('body')
    
  @show

  @section('scripts')
    <!-- Base scripts -->
    {{ HTML::script('js/jquery.min.js'); }}
    {{ HTML::script('js/bootstrap.min.js'); }}
    {{ HTML::script('js/jquery.gridster.with-extras-CUSTOM.js'); }}
    {{ HTML::script('js/underscore-min.js'); }}
    {{ HTML::script('js/jquery.ba-resize.min.js'); }}
    {{ HTML::script('js/jquery.fittext.js'); }}
    {{ HTML::script('js/jquery.growl.js'); }}
    <!-- /Base scripts -->

    <!-- Page specific modals -->
    @section('pageModals')
    @show
    <!-- /Page specific modals -->

    <!-- Page specific scripts -->
    @section('pageScripts')
    @show
    <!-- /Page specific scripts -->

    <!-- Widget specific scripts -->
    @section('widgetScripts')
    @show
    <!-- /Widget specific scripts -->

    @section ('pageAlert')
      @include('meta.pageAlerts')
    @show
  @show
     
</html>
