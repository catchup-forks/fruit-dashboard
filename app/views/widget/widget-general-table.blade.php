<div class="panel-transparent panel fill">
  <div class="panel-body fill">
  <table border=1 class="fill" id="table-{{ $widget->id }}">
    <thead>
      <tr>
      @foreach (array_keys($widget->dataManager()->getHeader()) as $name)
        <th>{{ $name }}</th>
      @endforeach
      </tr>
      <tbody>
      @foreach ($widget->dataManager()->getContent() as $row)
        <tr>
        @foreach ($row as $value)
          <td>{{ $value }}</td>
        @endforeach
        </tr>
      @endforeach
      </tbody>
    </thead>
  </table>
  </div>
</div>

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