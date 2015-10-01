<div class="widget-inner">
  <h4 class="drop-shadow text-white no-margin-top">{{ $title }}</h4>
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