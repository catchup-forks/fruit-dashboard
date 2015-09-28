<div class="chart-data">
  <div class="chart-name">
    {{ $widget->getSettings()['name'] }}
  </div> <!-- /.chart-name -->
  <div class="chart-value">
    @if ($widget->state == 'active')
      {{ $widget->getLatestData()['value'] }}
    @endif
  </div> <!-- /.chart-value -->
</div> <!-- /.chart-data -->

<div class="chart-diff-data text-center">

  @if (array_values($widget->dataManager()->compare($widget->getSettings()['resolution'], 1))[0] >= 0)
    <div class="chart-diff text-success">
      <span class="fa fa-arrow-up chart-diff-icon"> </span>

  @else
    <div class="chart-diff text-danger">
      <span class="fa fa-arrow-down chart-diff-icon"> </span>

  @endif

    <span class="chart-diff-value">{{ array_values($widget->dataManager()->compare($widget->getSettings()['resolution'], 1))[0] }}</span>
  </div> <!-- /.chart-diff -->


  <div class="chart-diff-dimension">
    <small>(1 {{ $widget->getSettings()['resolution'] }} ago)</small>
  </div> <!-- /.chart-diff-dimension -->
</div> <!-- /.chart-diff-data -->

<div id="chart-container-{{ $widget->id }}" class="clickable">
  <canvas id="chart-{{ $widget->id }}" class="chart chart-line"></canvas>
</div>

@section('widgetScripts')
<script type="text/javascript">
  // Set chart data
  var chartData{{ $widget->id }} = {
    'labels': [@foreach ($widget->getData() as $histogramEntry) "{{$histogramEntry['datetime']}}", @endforeach],
    'datasets': [{
      'values': [@foreach ($widget->getData() as $histogramEntry) {{$histogramEntry['value']}}, @endforeach],
      'color': '{{ SiteConstants::getChartJsColors()[0] }}'
    }]
  }
</script>
<script type="text/javascript">
  $(document).ready(function(){
    // Default values.
    var canvas = $("#chart-{{ $widget->id }}");
    var container = $('#{{ $widget->id }}-chart-container');
    var valueSpan = $("#{{ $widget->id }}-value");
    var name = "{{ $widget->descriptor->name }}";

    @if ($widget->state == 'active')
      // Removing the canvas and redrawing for proper sizing.
      canvas = reinsertCanvas(canvas);

      // Set chart data
      var chartData = {
        'labels': [@foreach ($widget->getData() as $histogramEntry) "{{$histogramEntry['datetime']}}", @endforeach],
        'datasets': [{
          'values': [@foreach ($widget->getData() as $histogramEntry) {{$histogramEntry['value']}}, @endforeach],
          'color': '{{ SiteConstants::getChartJsColors()[0] }}'
        }]
      }

      // Set chart options
      // var chartOptions = {
      //   'type': 'line',
      //   'chartJSOptions': globalChartOptions.getLineChartOptions()
      // }
        
      // Draw chart
      //new FDChart('{{ $widget->id }}').draw(chartData, chartOptions);

    @elseif ($widget->state == 'loading')
      // Loading widget.
      // values = [];
      // labels = [];
      // loadWidget({{$widget->id}}, function (data) {
      //   histogram = updateHistogramWidget(data, canvas, name, valueSpan);
      //   values = histogram['values'];
      //   labels = histogram['labels'];
      //   canvas = reinsertCanvas(canvas);
      // });
    @endif

    // Calling drawer every time carousel is changed.
    $('.carousel').on('slid.bs.carousel', function () {
      //canvas = reinsertCanvas(canvas);
      //new FDChart('{{ $widget->id }}').draw(chartData, chartOptions);
    })

    // Bind redraw to resize event.
    container.bind('resize', function(e){
      //canvas = reinsertCanvas(canvas);
      //new FDChart('{{ $widget->id }}').draw(chartData, chartOptions);
    });

    // Adding refresh handler.
    $("#refresh-{{$widget->id}}").click(function () {
      // refreshWidget({{ $widget->id }}, function (data) {
      //   updateHistogramWidget(data, canvas, name, valueSpan);
      // });
     });

    // Detecting clicks and drags.
    // Redirect to single stat page on click.
    // var isDragging = false;
    // container
    //   .mousedown(function() {
    //       isDragging = false;
    //   })
    //   .mousemove(function() {
    //       isDragging = true;
    //    })
    //   .mouseup(function() {
    //       var wasDragging = isDragging;
    //       isDragging = false;
    //       if (!wasDragging) {
    //         window.location = "{{ route('widget.singlestat', $widget->id) }}";
    //       }
    //   });

  });
</script>
@append