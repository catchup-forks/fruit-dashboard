  <div class="text-center">
  	<h1 id="timerTime" class="no-margin text-white drop-shadow text-center">{{ $widget->getSettings()['countdown']}}</h1>
    <button id="start" class="btn btn-primary">START!</button>
    <button id="reset" class="btn btn-primary" style="display:none">RESET!</button>
  </div> <!-- /#digitTime -->

@section('widgetScripts')

 <!-- script for clock -->
 <script type="text/javascript">
  $(document).ready(function() {
    var running = false;
    function reset() {
      running = false;
      $("#timerTime").html({{ $widget->getSettings()['countdown'] }});
      $("#start").show();
      $("#reset").hide();
    }
    function countdown() {
      if (!running) {
        return;
      }
      seconds = $("#timerTime").html();
      seconds = parseInt(seconds, 10);
      if (seconds == 1) {
        return;
      }
      seconds --;
      $("#timerTime").html(seconds);
      setTimeout(countdown, 1000);
    }

    $("#start").click(function () {
      running = true;
      $("#start").hide();
      $("#reset").show();
      countdown();
    });

    $("#reset").click(function () {
      reset();
    });

   });
 </script>
 <!-- /script for clock -->

@append