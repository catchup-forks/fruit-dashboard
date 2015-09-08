<div class="text-center margin-top-sm">
  <span class="text-white drop-shadow">
      {{ $widget->descriptor->name }}
  </span>
  <span class="text-white drop-shadow pull-right has-margin-horizontal" id="{{$widget->id}}-value">
  {{ $widget->getLatestData()['value'] }}
  </span>
</div>
<div id="{{ $widget->id }}-chart-container" class="has-margin-horizontal">
  <canvas id="{{$widget->id}}-chart"></canvas>
</div>
<div class="text-center drop-shadow text-white">
    Click
   <a href="{{ route('widget.singlestat', $widget->id) }}">here </a> for more details.
</div>

@section('widgetScripts')
<script type="text/javascript">
  $(document).ready(function(){
    // Default values.
    var canvas = $("#{{ $widget->id }}-chart");

    var canvasHeight = canvas.closest('li').height()*0.75;
    var canvasWidth = canvas.closest('li').width()*0.95;

    var container = $('#{{ $widget->id }}-chart-container');

    var valueSpan = $("#{{ $widget->id }}-value");
    var name = "{{ $widget->descriptor->name }}";

    // Function reinsertCanvas empties the container and reinserts a canvas. If measure is true then it updates the sizing variables.
    function reinsertCanvas(measure) {
      if (measure) {
        canvasHeight = canvas.closest('li').height()*0.75;
        canvasWidth = canvas.closest('li').width()*0.95;
      };

      container.empty();
      container.append('<canvas id=\"{{$widget->id}}-chart\" height=\"' + canvasHeight +'\" width=\"' + canvasWidth + '\"></canvas>');

      canvas = $("#{{ $widget->id }}-chart");
    }

    @if ($widget->state == 'active')
      // Active widget.
      var labels =  [@foreach ($widget->getData() as $histogramEntry) "{{$histogramEntry['date']}}", @endforeach];
      var values = [@foreach ($widget->getData() as $histogramEntry) {{$histogramEntry['value']}}, @endforeach];

      // Removing the canvas and redrawing for proper sizing.
      reinsertCanvas(false);

      // Calling drawer for the first time.
      drawLineGraph(canvas, [{'values': values, 'name': 'All'}], labels, name);

    @endif

    @if ($widget->state == 'loading')
      // Loading widget.
      loadWidget({{$widget->id}}, function (data) {updateHistogramWidget(data, canvas, name, valueSpan); });
    @endif

    // Calling drawer every time carousel is changed.
    $('.carousel').on('slid.bs.carousel', function () {
      reinsertCanvas(false);

      drawLineGraph(canvas, [{'values': values, 'name': 'All'}], labels, name);
    })

    // Bind redraw to resize event.
    $('#widget-wrapper-{{$widget->id}}').bind('resize', function(e){

      reinsertCanvas(true);

      drawLineGraph(canvas, [{'values': values, 'name': 'All'}], labels, name);
    });

    function updateHistogramWidget(data, canvas, name, valueSpan) {
      // Updating chart values.
      var labels = [];
      var values = [];
      for (i = 0; i < data.length; ++i) {
        labels.push(data[i]['date']);
        values.push(data[i]['value']);
      }
      if (data.length > 0 && valueSpan) {
        valueSpan.html(data[data.length-1]['value']);
      }

      reinsertCanvas(false);

      canvas = $("#{{ $widget->id }}-chart");

      drawLineGraph(canvas, [{'values': values, 'name': 'All'}], labels, name);
    }

    // Adding refresh handler.
    $("#refresh-{{$widget->id}}").click(function () {
      refreshWidget({{ $widget->id }}, function (data) { updateHistogramWidget(data, canvas, name, valueSpan);});
     });

  });
</script>

@append