<div class="widget-inner fill" id="widget-loading-{{ $widget['id'] }}">
  <div class="widget-heading larger-text">
    {{ $widget['descriptor']['name'] }}
  </div> <!-- /.widget-heading -->
  <p class="lead text-center">
    There was an issue while rendering your widget
  </p>
  <p class="text-center">You can try to reset it
    <a href="{{ URL::route('widget.reset', $widget['id']) }}">here</a>.
  </p>
  <p class="text-center">
    This can happen due to a misconfigured widget, or it's a problem at our end.<br>
    We're looking into the problem.
  </p>
</div> <!-- /.widget-inner -->

@section('widgetScripts')
<script type="text/javascript">
  // Set chart data
  var widgetData{{ $widget['id'] }} = {};
</script>
@append
