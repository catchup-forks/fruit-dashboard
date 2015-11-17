@if ($widget['layout'] == 'table')
  @include('widget.widget-general-table')
@elseif ($widget['layout'] == 'count')
  @include('widget.widget-general-count')
@else
  @include('widget.widget-general-chart')
@endif

@section('widgetScripts')
<script type="text/javascript">
  // Set Widget default data
  var widgetData{{ $widget['id'] }} = {{ json_encode($widget['data']) }}
</script>
@append