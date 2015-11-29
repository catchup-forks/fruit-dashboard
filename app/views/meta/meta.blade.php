<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="chrome-webstore-item" href="https://chrome.google.com/webstore/detail/cgmdkfkbilmbclifhmfgabbkkcfjcicp">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

    <title>
      Fruit Dashboard
      @if (trim($__env->yieldContent('pageTitle')))
        | @yield('pageTitle')
      @endif
    </title>

    @section('tracking')
      @if(GlobalTracker::isTrackingEnabled())
        @include('tracking.global-event-tracker')
      @endif
    @show

    @section('stylesheet')
      <!-- Fonts -->
      <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,400italic,700,800' rel='stylesheet' type='text/css'>
      <!-- /Fonts -->

      <!-- General CSS -->
      {{ Minify::stylesheet(array(
            '/css/bootstrap.min.css', 
            '/css/jquery.gridster.min.css',
            '/css/jquery.growl.css',
            '/css/hopscotch.min.css',
            '/css/custom.css'
         )) 
      }}      
      <!-- /General CSS -->

      <!-- Font Awesome CSS -->
      {{ HTML::style('css/font-awesome.min.css') }}
      <!-- /Font Awesome CSS -->

      <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
      <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <![endif]-->

      <!-- Page specific stylesheet -->
      @section('pageStylesheet')
      @show
      <!-- /Page specific stylesheet -->
    @show
  </head>


  @section('body')

  @show

  @section('scripts')
    <!-- General JS -->
    {{ Minify::javascript(array(
          '/js/jquery.min.js',
          '/js/jquery.fittext-CUSTOM.js',
          '/js/bootstrap.min.js',
          '/js/jquery.gridster.min.js',
          '/js/underscore-min.js',
       )) 
    }}      
    {{ Minify::javascript(array(
          '/js/jquery.ba-resize.min.js',
          '/js/jquery.growl.js',
          '/js/moment.min.js',
          '/js/Chart2.js',
          '/js/jstz.min.js',
          '/js/hopscotch.min.js',
          '/js/bootbox.min.js'
       )) 
    }}      
    <!-- /General JS -->

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

    <!-- Page alerts -->
    @section('pageAlert')
      @include('meta.page-alerts')
    @show
    <!-- /Page alerts -->

    <!-- TimeZone detection -->
    @section ('timezoneDetection')
      @include('meta.timezone-detection')
    @show
    <!-- TimeZone detection -->    
  @show

</html>
