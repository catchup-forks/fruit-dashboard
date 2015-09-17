<h2 class="text-white drop-shadow has-margin-horizontal text-center" id="{{$widget->id}}-value">
  {{ var_dump($widget->getCurrentValue()) }}
</h2>

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