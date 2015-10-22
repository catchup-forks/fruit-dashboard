  <div class="text-center">
  	<h1 id="timer-time-{{ $widget['id'] }}" class="no-margin text-white drop-shadow text-center">{{ $widget['settings']['countdown']}}</h1>
    <button id="start-{{ $widget['id'] }}" class="btn btn-primary">START!</button>
    <button id="reset-{{ $widget['id'] }}" class="btn btn-primary" style="display:none">RESET!</button>
  </div> <!-- /#digitTime -->

@section('widgetScripts')

 <!-- script for timer -->
 <script type="text/javascript">
  var widgetData{{ $widget['id'] }} = {
    countdown: "{{ $widget['settings']['countdown'] }}"
  }
 </script>
 <!-- /script for clock -->

@append