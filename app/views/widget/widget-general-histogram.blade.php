<div class="text-center margin-top-sm">
  <span class="text-white drop-shadow">
      {{ $widget->descriptor->name }}
  </span>
  <span class="text-white drop-shadow pull-right has-margin-horizontal" id="{{$widget->id}}-value">
  @if ($widget->state == 'active')
    {{ $widget->getLatestData()['value'] }}
  @endif
  </span>
</div>
<div id="{{ $widget->id }}-chart-container" class="has-margin-horizontal">
  <canvas id="{{$widget->id}}-chart"></canvas>
</div>
<div class="text-center drop-shadow text-white">
    Click
   <a href="{{ route('widget.singlestat', $widget->id) }}" class="btn btn-primary btn-xs">here </a> for more details.
</div>

@section('widgetScripts')
<script type="text/javascript">
  $(document).ready(function(){
    // Default values.
    var canvas = $("#{{ $widget->id }}-chart");
    var container = $('#{{ $widget->id }}-chart-container');
    var valueSpan = $("#{{ $widget->id }}-value");
    var name = "{{ $widget->descriptor->name }}";

    @if ($widget->state == 'active')
      // Active widget.
      var labels =  [@foreach ($widget->getData() as $histogramEntry) "{{$histogramEntry['datetime']}}", @endforeach];
      var values = [@foreach ($widget->getData() as $histogramEntry) {{$histogramEntry['value']}}, @endforeach];

      // Removing the canvas and redrawing for proper sizing.
      canvas = reinsertCanvas(canvas);

      // Calling drawer for the first time.
      drawLineGraph(canvas, [{'values': values, 'name': 'All'}], labels, name);

    @elseif ($widget->state == 'loading')
      // Loading widget.
      loadWidget({{$widget->id}}, function (data) {updateHistogramWidget(data, canvas, name, valueSpan); });
    @endif

    // Calling drawer every time carousel is changed.
    $('.carousel').on('slid.bs.carousel', function () {
      canvas = reinsertCanvas(canvas);

      drawLineGraph(canvas, [{'values': values, 'name': 'All'}], labels, name);
    })

    // Bind redraw to resize event.
    $('#widget-wrapper-{{$widget->id}}').bind('resize', function(e){
      canvas = reinsertCanvas(canvas);
      drawLineGraph(canvas, [{'values': values, 'name': 'All'}], labels, name);
    });

    // Adding refresh handler.
    $("#refresh-{{$widget->id}}").click(function () {
      refreshWidget({{ $widget->id }}, function (data) { updateHistogramWidget(data, canvas, name, valueSpan);});
     });

  });
</script>

@append