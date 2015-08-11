<div class="panel-transparent">
  <div class="text-center panel-heading">
    <span class="text-white drop-shadow">
       {{ $widget->descriptor->name }}
    </span>
    <span class="text-white drop-shadow pull-right" id="{{$widget->descriptor->type}}-value">
      ${{ $widget->getLatestData()['value'] }}
    </span>
  </div>
  <div class="panel-body">
    <canvas id="{{$widget->descriptor->type}}-chart"></canvas>
  </div>
</div>

@section('widgetScripts')
<script type="text/javascript">
  $(document).ready(function(){
    // Default values.
    var canvas = $("#{{ $widget->descriptor->type }}-chart");
    var valueSpan = $("#{{ $widget->descriptor->type }}-value");
    var name = "{{ $widget->descriptor->name }}";

    @if ($widget->state == 'active')
      // Active widget.
      var labels =  [@foreach ($widget->getData() as $histogramEntry) "{{$histogramEntry['date']}}", @endforeach];
      var values = [@foreach ($widget->getData() as $histogramEntry) {{$histogramEntry['value']}}, @endforeach];

      // Calling drawer.
       drawLineGraph(canvas, values, labels, name, 3000);
    @endif

    @if ($widget->state == 'loading')
      // Loading widget.
      loadWidget({{$widget->id}}, function (data) {updateChartWidget(data, canvas, name, valueSpan); });
    @endif

    // Bind redraw to resize event.
    $('#widget-wrapper-{{$widget->id}}').bind('resize', function(e){
        drawLineGraph(canvas, values, labels, name, 0);
    });

    // Adding refresh handler.
    $("#refresh-{{$widget->id}}").click(function () {
      refreshWidget({{ $widget->id }}, function (data) { updateChartWidget(data, canvas, name, valueSpan);});
     });

  });

</script>

@append