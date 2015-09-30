<div>
  <h3 id="digital-clock-{{ $widget->id }}" class="no-margin-top has-margin-vertical-sm text-white drop-shadow text-center truncate">{{ $widget['currentTime'] }}
  </h3>    
</div>

@section('widgetScripts')
 <!-- script for clock -->
 <script type="text/javascript">
  var widgetData{{ $widget->id }} = {
    'type': "{{ $widget->getSettings()['clock_type'] }}"
  }
 </script>
 <!-- /script for clock -->
@append