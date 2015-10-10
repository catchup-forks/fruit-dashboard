<div class="widget-inner">
  <div class="widget-heading larger-text">
    {{ $title }}
  </div> <!-- /.widget-heading -->
  <table class="table table-condensed table-bordered">
    <thead>
    @foreach ($widget['header'] as $key=>$name)
      <th>{{ $name }}</th>
    @endforeach
    </thead>
    <tbody>
    @foreach ($widget['content'] as $row)
      <tr>
        @foreach ($row as $col) <td>{{ $col }}</td> @endforeach
      </tr>
    @endforeach
    </tbody>
  </table>
</div> <!-- /.widget-inner -->

@section('widgetScripts')
<script type="text/javascript">
  // Set chart data
  var widgetData{{ $widget['id'] }} = {
    'header': [@foreach ($widget['header'] as $key => $num)'{{ $key }}',@endforeach],
    'content': [@foreach ($widget['content'] as $row)[@foreach ($row as $col)'{{ $col }}',@endforeach],@endforeach]
  }
</script>
@append