<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 

    @section('stylesheet')
      <!-- Fonts -->
      <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,400italic,700,800' rel='stylesheet' type='text/css'>
      <!-- /Fonts -->
      {{ Minify::stylesheetDir('/css')->withFullUrl() }}
    @show
  </head>

  @section('body')
    <body>
      @section('pageContent')
      @show
    </body>
  @show

  @section('scripts')
    <!-- General libs -->
    {{ Minify::javascriptDir('/js')->withFullUrl() }}
    <!-- /General libs -->

    <!-- Page specific scripts -->
    @section('pageScripts')
    @show
    <!-- /Page specific scripts -->
  @show

</html>
