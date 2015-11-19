<div class="widget-inner fill" id="widget-loading-{{ $widget['id'] }}">
  <div class="widget-heading larger-text">
    {{ $widget['descriptor']['name'] }}
  </div> <!-- /.widget-heading -->
  <p class="lead text-center">
    This widget has invalid settings, that can't be recovered.
  </p>
  <p class="text-center">You can try to reset it
    <a href="{{ URL::route('widget.reset', $widget['id']) }}">here</a>.
  </p>
</div> <!-- /.widget-inner -->

@section('widgetScripts')
<script type="text/javascript">
  // Set chart data
  var widgetData{{ $widget['id'] }} = {};
</script>
@append
