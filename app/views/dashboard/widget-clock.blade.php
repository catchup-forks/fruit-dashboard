  <div id="digitClock">
  	<h1 id="digitTime" class="no-margin text-white drop-shadow text-center">{{ $currentTime }}</h1>
  </div> <!-- /#digitTime -->

@section('widgetScripts')

 <!-- script for clock -->
 <script type="text/javascript">
   $(document).ready(function() {
   	function startTime() {
       var today = new Date();
       var h = today.getHours();
       var m = today.getMinutes();
       m = checkTime(m);
       h = checkTime(h);
       $('#digitTime').html(h + ':' + m);
       var t = setTimeout(function(){startTime()},500);
     }

     function checkTime(i) {
       if (i<10){i = "0" + i};  // add zero in front of numbers < 10
       return i;
     }

     startTime();
     // fit the clock on page load
     $('#digitTime').fitText(0.3);

      // bind fittext to a resize event
      $('#digitClock').bind('resize', function(e){
        $('#digitTime').fitText(0.3);
      })

   });
 </script>
 <!-- /script for clock -->
 
@append