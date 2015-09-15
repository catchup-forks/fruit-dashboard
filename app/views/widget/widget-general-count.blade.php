<span class="text-white drop-shadow has-margin-horizontal" id="{{$widget->id}}-value">
  {{ var_dump($widget->getCurrentValue()) }}
</span>

@section('widgetScripts')
<script type="text/javascript">
  $(document).ready(function(){
    // Adding refresh handler.
    $("#refresh-{{$widget->id}}").click(function () {
      refreshWidget({{ $widget->id }}, function (data) {console.log(data)});
     });
  });
</script>
@append