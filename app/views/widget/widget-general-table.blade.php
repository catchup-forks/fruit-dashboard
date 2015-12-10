
<div class="flex-child">
  <div class="widget-heading larger-text">
    {{ $widget['name'] }}
  </div> <!-- /.widget-heading -->
  <table class="table table-condensed table-bordered text-center"></table>
</div> <!-- /.widget-inner -->

@section('widgetScripts')
<script type="text/javascript">
  // Set Widget default data
  var widgetData{{ $widget['id'] }} = {{ json_encode($widget['data']) }}
</script>
@append
