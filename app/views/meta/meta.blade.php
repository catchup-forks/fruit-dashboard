<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="google-site-verification" content="OcCyih2_Yrpb4aX_HO-XedRzXBIQcuesia2h2O2oYPo" />
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
      <link href='https://fonts.googleapis.com/css?family=Roboto:400,100,300,500,700&subset=latin,latin-ext' rel='stylesheet' type='text/css'>
      <!-- /Fonts -->

      <!-- Bootstrap core CSS -->
      {{ HTML::style('css/bootstrap.min.css') }}
      <!-- /Bootstrap -->

      <!-- Font Awesome CSS -->
      {{ HTML::style('css/font-awesome.min.css') }}
      <!-- /FontAwesome -->

      <!-- PaymentFonts CSS -->
      {{ HTML::style('css/paymentfont.min.css') }}
      <!-- /PaymentFonts CSS -->

      <!-- PixelAdmin -->
      {{ HTML::style('css/pixel-admin.min.css') }}
      {{ HTML::style('css/themes.min.css') }}
      {{ HTML::style('css/widgets.min.css') }}
      {{ HTML::style('css/pages.min.css') }}
      <!-- /PixelAdmin -->

      <!-- Gridster -->
      {{ HTML::style('css/jquery.gridster.min.css') }}
      <!-- /Gridster -->

      <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
      <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <![endif]-->

      <!-- Custom styles -->
      {{ HTML::style('css/custom.css') }}
      <!-- /Custom styles -->

      <script type="text/javascript">
        var init = [];
      </script>
      <!-- /Pixeladmin js init array -->
      
      <!-- GoogleAnalyticsEvents -->
      {{ HTML::script('js/google_events.js'); }}
      <!-- /GoogleAnalyticsEvents -->

      <!-- Mixpanel event -->
      {{ HTML::script('js/mixpanel_event.js') }}
      <!-- / Mixpanel event -->

      <!-- Mixpanel user tracking -->
      {{ HTML::script('js/mixpanel_user.js') }}
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
    {{ HTML::script('js/jquery.js'); }}
    {{ HTML::script('js/bootstrap.min.js'); }}
    {{ HTML::script('js/pixel-admin.min.js'); }}
    {{ HTML::script('js/chart.min.js'); }}
    {{ HTML::script('js/jquery.gridster.with-extras.min.js'); }}
    {{ HTML::script('js/underscore-min.js'); }}
    {{ HTML::script('js/jquery.ba-resize.js'); }}
    {{ HTML::script('js/jquery.fittext.js'); }}
    <!-- /Base scripts -->

    <!-- Pagealert timeout -->
    <script type="text/javascript">
      $(document).ready(function(){

      });
    </script>
    <!-- /Pagealert timeout -->

    <!-- Page specific modals -->
    @section('pageModals')
    @show
    <!-- /Page specific modals -->

    <!-- Page specific scripts -->
    @section('pageScripts')
    @show
    <!-- /Page specific scripts -->

    <!-- PixelAdmin js start -->
    <script type="text/javascript">
      window.PixelAdmin.start(init);
    </script>
    <!-- /PixelAdmin js start -->

    <!-- GoogleAnalytics -->
    {{ HTML::script('js/google_analytics.js'); }}
    <!-- GoogleAnalytics -->
  @show
     
</html>
