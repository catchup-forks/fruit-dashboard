<div class="chart-data">
  <div class="chart-name">
    {{ $widget->getSettings()['name'] }}
  </div> <!-- /.chart-name -->

</div> <!-- /.chart-data -->

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
        'labels': [@foreach ($widget->getData()['datetimes'] as $histogramEntry) "{{$histogramEntry}}", @endforeach],
        'datasets': [
          @foreach ($widget->getData()['datasets'] as $dataset)
            {
              'values' : [{{ implode(',', $dataset['values']) }}],
              'name' : "{{ $dataset['name'] }}",
              'color': "{{ $dataset['color'] }}"
            },
          @endforeach
        ]
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
      // datasets = [];
      // labels = [];
      // loadWidget({{$widget->id}}, function (data) {
      //   updateMultipleHistogramWidget(data, canvas, name, valueSpan);
      //   datasets = data['datasets'];
      //   labels = data['datetimes'];
      //   canvas = $("#{{ $widget->id }}-chart");
      // });
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
      // refreshWidget({{ $widget->id }}, function (data) {
      //   updateMultipleHistogramWidget(data, canvas, name, valueSpan);
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