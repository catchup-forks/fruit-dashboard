  <div class="text-center">
  	<h1 id="timer-time-{{ $widget->id }}" class="no-margin text-white drop-shadow text-center">{{ $widget->getSettings()['countdown']}}</h1>
    <button id="start-{{ $widget->id }}" class="btn btn-primary">START!</button>
    <button id="reset-{{ $widget->id }}" class="btn btn-primary" style="display:none">RESET!</button>
  </div> <!-- /#digitTime -->

@section('widgetScripts')

 <!-- script for clock -->
 <script type="text/javascript">
  $(document).ready(function() {
    var running = false;
    function reset() {
      running = false;
      $("#timer-time-{{ $widget->id }}").html({{ $widget->getSettings()['countdown'] }});
      $("#start-{{ $widget->id }}").show();
      $("#reset-{{ $widget->id }}").hide();
    }
    function countdown() {
      if (!running) {
        return;
      }
      seconds = $("#timer-time-{{ $widget->id }}").html();
      seconds = parseInt(seconds, 10);
      if (seconds == 1) {
        return;
      }
      seconds --;
      $("#timer-time-{{ $widget->id }}").html(seconds);
      setTimeout(countdown, 1000);
    }

    $("#start-{{ $widget->id }}").click(function () {
      running = true;
      $("#start-{{ $widget->id }}").hide();
      $("#reset-{{ $widget->id }}").show();
      countdown();
    });

    $("#reset-{{ $widget->id }}").click(function () {
      reset();
    });

   });
 </script>
 <!-- /script for clock -->

@append