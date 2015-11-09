<div class="chart-value larger-text">
  {{ Utilities::formatNumber(array_values($widget['data']['currentValue'])[0], $widget['format']) }}
</div> <!-- /.chart-value -->

<div class="chart-diff-data text-center">

  <div class="chart-diff @if($widget['className']::isSuccess($widget['data']['currentDiff'])) text-success @else text-danger @endif">
  @if ($widget['data']['currentDiff'] >= 0)
      <span class="fa fa-arrow-up chart-diff-icon"> </span>
  @else
      <span class="fa fa-arrow-down chart-diff-icon"> </span>
  @endif
    <span class="chart-diff-value larger-text">{{ Utilities::formatNumber(array_values($widget['data']['currentDiff'])[0], $widget['format']) }}</span>
  </div> <!-- /.chart-diff -->

  <div class="chart-diff-dimension smaller-text">
    <small>(a {{ rtrim($widget['settings']['resolution'], 's') }} ago)</small> 
  </div> <!-- /.chart-diff-dimension -->
</div> <!-- /.chart-diff-data -->

<p class="chart-name text-center">
  {{ $widget['settings']['name'] }}
</p> <!-- /.chart-name -->

<div id="chart-container-{{ $widget['id'] }}" class="clickable">
  <canvas class="chart chart-line"></canvas>
</div>

@section('widgetScripts')
<script type="text/javascript">
  // Set chart data
  var widgetData{{ $widget['id'] }} = {
    'isCombined' : {{$widget['data']['isCombined']}},
    'labels': [@foreach ($widget['data']['labels'] as $datetime) "{{$datetime}}", @endforeach],
    'datasets': [
    @foreach ($widget['data']['datasets'] as $dataset)
      {
          'type' : '{{ $dataset['type'] }}',
          'values' : [{{ implode(',', $dataset['values']) }}],
          'name' : "{{ $dataset['name'] }}",
          'color': "{{ $dataset['color'] }}"
      },
    @endforeach
    ]
  }
  $("#widget-wrapper-{{ $widget['id'] }}").hover(function(e){
    var widget = $(e.currentTarget);
    widget.find('.chart-value').css('visibility', 'hidden');
    widget.find('.chart-diff-data').css('visibility', 'hidden');
    widget.find('.chart-name').css('visibility', 'hidden');
  }, function(e){
    var widget = $(e.currentTarget);
    widget.find('.chart-value').css('visibility', 'visible');
    widget.find('.chart-diff-data').css('visibility', 'visible');
    widget.find('.chart-name').css('visibility', 'visible');
  });
</script>
@append
