<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 

    @section('stylesheet')
      <!-- Fonts -->
      <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,400italic,700,800' rel='stylesheet' type='text/css'>
      <!-- /Fonts -->

      <!-- Bootstrap CSS -->
      {{ HTML::style('css/bootstrap.min.css') }}
      <!-- /Bootstrap CSS-->

      <!-- Font Awesome CSS -->
      {{ HTML::style('css/font-awesome.min.css') }}
      <!-- /FontAwesome CSS-->
    @show
  </head>

  @section('body')
    <body>
      @section('pageContent')
      @show
    </body>
  @show

  @section('scripts')
    <!-- Base scripts -->
    {{ HTML::script('js/jquery.min.js'); }}
    {{ HTML::script('js/bootstrap.min.js'); }}
    {{ HTML::script('js/Chart.js'); }}
    <!-- /Base scripts -->

    <!-- Page specific scripts -->
    @section('pageScripts')
    @show
    <!-- /Page specific scripts -->
  @show

</html>
