@if ( ! $widget->premiumUserCheck())
  @include('widget.widget-premium-not-allowed', ['feature' => $widget->getSettings()['resolution'] . ' statistics'])
@else
  <div class="padding text-center">
    <span class="text-white drop-shadow">
        {{ $widget->descriptor->name }}
    </span>
  </div>
  <div id="{{ $widget->id }}-chart-container" class="has-margin-horizontal clickable">
    <canvas id="{{$widget->id}}-chart" class="chart chart-line"></canvas>
  </div>
@endif

@section('widgetScripts')
@if ($widget->premiumUserCheck())
  <script type="text/javascript">
    $(document).ready(function(){
      // Default values.
      var canvas = $("#{{ $widget->id }}-chart");
      var container = $('#{{ $widget->id }}-chart-container');
      var valueSpan = $("#{{ $widget->id }}-value");
      var name = "{{ $widget->descriptor->name }}";

      @if ($widget->state == 'active')
        // Active widget.
        var labels =  [@foreach ($widget->getData()['datetimes'] as $histogramEntry) "{{$histogramEntry}}", @endforeach];
        var datasets = [
          @foreach ($widget->getData()['datasets'] as $dataset)
            {
              'values' : [{{ implode(',', $dataset['values']) }}],
              'name' : '{{ $dataset['name'] }}',
              'color': '{{ $dataset['color'] }}'
            },
          @endforeach
        ];

        // Removing the canvas and redrawing for proper sizing.
        canvas = reinsertCanvas(canvas);

        // Calling drawer for the first time.
        drawLineGraph(canvas, datasets, labels, name);

      @elseif ($widget->state == 'loading')
        // Loading widget.
        datasets = [];
        labels = [];
        loadWidget({{$widget->id}}, function (data) {
          updateMultipleHistogramWidget(data, canvas, name, valueSpan);
          datasets = data['datasets'];
          labels = data['datetimes'];
          canvas = $("#{{ $widget->id }}-chart");
        });
      @endif

      // Calling drawer every time carousel is changed.
      $('.carousel').on('slid.bs.carousel', function () {
        canvas = reinsertCanvas(canvas);

        drawLineGraph(canvas, datasets, labels, name);
      })

      // Bind redraw to resize event.
      $('#widget-wrapper-{{$widget->id}}').bind('resize', function(e){
        chartOptions.animation = false;
        canvas = reinsertCanvas(canvas);
        drawLineGraph(canvas, datasets, labels, name);
        chartOptions.animation = true;
      });

      // Adding refresh handler.
      $("#refresh-{{$widget->id}}").click(function () {
        refreshWidget({{ $widget->id }}, function (data) { updateMultipleHistogramWidget(data, canvas, name, valueSpan);});
        canvas = $("#{{ $widget->id }}-chart");
       });

      // Detecting clicks and drags.
      // Redirect to single stat page on click.
      var isDragging = false;
      $('#{{ $widget->id }}-chart-container')
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
@endif
@append