<div class="chart-data">
  <div class="chart-name">
    {{ $widget->getSettings()['name'] }}
  </div> <!-- /.chart-name -->
  <div class="chart-value">
    @if ($widget->state == 'active')
      @if ( ! $widget->hasCumulative())
      {{ Utilities::formatNumber($widget->getLatestValues()['value'], $widget->getFormat()) }}
      @else
      {{ Utilities::formatNumber($widget->getDiff($widget->getSettings()['length'])['value'], $widget->getFormat()) }}
      @endif
    @endif
  </div> <!-- /.chart-value -->
</div> <!-- /.chart-data -->

<div class="chart-diff-data text-center">

  @if ($widget->getDiff()['value'] >= 0)
    <div class="chart-diff text-success">
      <span class="fa fa-arrow-up chart-diff-icon"> </span>
  @else
    <div class="chart-diff text-danger">
      <span class="fa fa-arrow-down chart-diff-icon"> </span>
  @endif
    <span class="chart-diff-value">{{ Utilities::formatNumber($widget->getDiff()['value'], $widget->getFormat()) }}</span>
  </div> <!-- /.chart-diff -->


  <div class="chart-diff-dimension">
    <small>(a {{ rtrim($widget->getSettings()['resolution'], 's') }} ago)</small>
  </div> <!-- /.chart-diff-dimension -->
</div> <!-- /.chart-diff-data -->

<div id="{{ $widget->id }}-chart-container" class="clickable">
  <canvas id="{{$widget->id}}-chart" class="chart chart-line"></canvas>
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
      // Removing the canvas and redrawing for proper sizing.
      canvas = reinsertCanvas(canvas);

      // Set chart data
      var chartData = {
        'labels': [@foreach ($widget->getData()['labels'] as $datetime) "{{$datetime}}", @endforeach],
        'datasets': [{
          'values': [@foreach ($widget->getData()['datasets'][0]['values'] as $value) {{ $value }}, @endforeach],
          'color': '{{ SiteConstants::getChartJsColors()[0] }}'
        }]
      }

      // Set chart options
      var chartOptions = {
        'type': 'line',
        'chartJSOptions': globalChartOptions.getLineChartOptions()
      }

      // Draw chart
      new FDChart('{{ $widget->id }}').draw(chartData, chartOptions);

    @elseif ($widget->state == 'loading')
      // Loading widget.
      values = [];
      labels = [];
      loadWidget({{$widget->id}}, function (data) {
        histogram = updateHistogramWidget(data, canvas, name, valueSpan);
        values = histogram['values'];
        labels = histogram['labels'];
        canvas = reinsertCanvas(canvas);
      });
    @endif

    // Calling drawer every time carousel is changed.
    $('.carousel').on('slid.bs.carousel', function () {
      canvas = reinsertCanvas(canvas);
      new FDChart('{{ $widget->id }}').draw(chartData, chartOptions);
    })

    // Bind redraw to resize event.
    container.bind('resize', function(e){
      canvas = reinsertCanvas(canvas);
      new FDChart('{{ $widget->id }}').draw(chartData, chartOptions);
    });

    // Adding refresh handler.
    $("#refresh-{{$widget->id}}").click(function () {
      refreshWidget({{ $widget->id }}, function (data) {
        updateHistogramWidget(data, canvas, name, valueSpan);
      });
     });

    // Detecting clicks and drags.
    // Redirect to single stat page on click.
    var isDragging = false;
    container
      .mousedown(function() {
          isDragging = false;
      })
      .mousemove(function() {
          isDragging = true;
       })
      .mouseup(function() {
          var wasDragging = isDragging;
          isDragging = false;
          if (!wasDragging) {
            window.location = "{{ route('widget.singlestat', $widget->id) }}";
          }
      });

  });
</script>
@append