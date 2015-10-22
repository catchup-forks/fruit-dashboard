<div class="widget-inner fill @if ($widget['state'] != 'loading') not-visible @endif" id="widget-loading-{{ $widget['id'] }}">
  <p class="lead text-center">
    <i class="fa fa-circle-o-notch fa-spin"></i> Loading...
  </p>
</div> <!-- /.widget-inner -->

@section('widgetScripts')
<script type="text/javascript">
  // Set chart data
  var widgetData{{ $widget['id'] }} = {};
</script>
@append
