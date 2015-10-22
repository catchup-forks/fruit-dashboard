<div class="widget-inner">
  <div class="widget-heading larger-text">
    {{ $title }}
  </div> <!-- /.widget-heading -->
  <table class="table table-condensed table-bordered"></table>
</div> <!-- /.widget-inner -->

@section('widgetScripts')
<script type="text/javascript">
  // Set table data
  var widgetData{{ $widget['id'] }} = {
    'type': 'table',
    'header': [@foreach ($widget['header'] as $key => $num)'{{ $key }}',@endforeach],
    'content': [@foreach ($widget['content'] as $row)[@foreach ($row as $col)'{{ $col }}',@endforeach],@endforeach]
  }
</script>
@append