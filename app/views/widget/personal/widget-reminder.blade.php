<div id="reminder-wrapper">
    <h1 id="reminder" class="no-margin text-white drop-shadow text-center">{{ $widget->getSettings()['text'] }}</h1>
</div>

@section('widgetScripts')

 <!-- script for clock -->
 <script type="text/javascript">
   $(document).ready(function() {
    // fit the clock on page load
     $('#reminder').fitText(0.3);

      // bind fittext to a resize event
      $('#reminder-wrapper').bind('resize', function(e){
        $('#reminder').fitText(0.3);
      })

   });
 </script>
 <!-- /script for clock -->

@append
