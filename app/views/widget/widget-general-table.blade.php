<div class="widget-inner">
  <table class="table table-condensed table-bordered">
    <thead>
      <tr class="active">
        @foreach (array_keys($widget->dataManager()->getHeader()) as $name)
          <th>{{ $name }}</th>
        @endforeach
      </tr>
    </thead>
    <tbody>
      @foreach ($widget->dataManager()->getContent() as $row)
        <tr>
        @foreach ($row as $value)
          <td>{{ $value }}</td>
        @endforeach
        </tr>
      @endforeach
    </tbody>
  </table>
</div> <!-- /.widget-inner -->

@section('widgetScripts')
<script type="text/javascript">
  $(document).ready(function(){
    // Adding refresh handler.
    $("#refresh-{{$widget->id}}").click(function () {
      refreshWidget({{ $widget->id }}, function (data) {
        updateTableWidget(data, 'table-{{$widget->id}}');
     });
   });
  });
</script>
@append