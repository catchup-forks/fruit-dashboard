<div class="widget-inner">
  <table class="table table-condensed table-bordered">
  </table>
</div> <!-- /.widget-inner -->

@section('widgetScripts')
<script type="text/javascript">
  // Set chart data
  var widgetData{{ $widget->id }} = {
    'header': [@foreach ($widget->getHeader() as $key => $num)'{{ $key }}',@endforeach],
    'content': [@foreach ($widget->getContent() as $row)[@foreach ($row as $value)'{{ $value }}',@endforeach],@endforeach]
  }
</script>
@append