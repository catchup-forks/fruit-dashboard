<div class="widget-inner">
  <div class="widget-heading larger-text">
    {{ $widget['name'] }}
  </div> <!-- /.widget-heading -->
  <table class="table table-condensed table-bordered"></table>
</div> <!-- /.widget-inner -->

@section('widgetScripts')
<script type="text/javascript">
  // Set table data
  var widgetData{{ $widget['id'] }} = {
    'header': [@foreach ($widget['data']['header'] as $head)'{{ $head }}',@endforeach],
    'content': [@foreach ($widget['data']['content'] as $row)[@foreach ($row as $col)'{{ $col }}',@endforeach],@endforeach]
  }
</script>
@append
