<div class="text-center margin-top-sm">
  <span class="text-white drop-shadow">
      {{ $widget->descriptor->name }}
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
      var labels = [];
      var datasets = [];
      loadWidget({{$widget->id}}, function (data) {updateMultipleHistogramWidget(data, canvas, name, valueSpan); });
    @endif

    // Calling drawer every time carousel is changed.
    $('.carousel').on('slid.bs.carousel', function () {
      canvas = reinsertCanvas(canvas);
      drawLineGraph(canvas, datasets, labels, name);
    })

    // Bind redraw to resize event.
    $('#widget-wrapper-{{$widget->id}}').bind('resize', function(e){
      canvas = reinsertCanvas(canvas);
      drawLineGraph(canvas, datasets, labels, name);
    });

    // Adding refresh handler.
    $("#refresh-{{$widget->id}}").click(function () {
      refreshWidget({{ $widget->id }}, function (data) { updateMultipleHistogramWidget(data, canvas, name, valueSpan);});
     });

  });
</script>

@append