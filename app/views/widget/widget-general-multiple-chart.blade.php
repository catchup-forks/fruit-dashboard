<p class="chart-name text-center">
  {{ $widget['settings']['name'] }}
</p> <!-- /.chart-name -->
@if (count($widget['data']['datasets']) == 1 || $widget['settings']['type'] == 'chart')
  <div class="chart-diff-data text-center">

    @if ($widget['defaultDiff'] >= 0)
      <div class="chart-diff text-success">
        <span class="fa fa-arrow-up chart-diff-icon"> </span>

    @else
      <div class="chart-diff text-danger">
        <span class="fa fa-arrow-down chart-diff-icon"> </span>

    @endif

      <span class="chart-diff-value larger-text">{{$widget['defaultDiff']}}</span>
    </div> <!-- /.chart-diff -->


    <div class="chart-diff-dimension smaller-text">
      <small>(a {{ rtrim($widget['settings']['resolution'], 's') }} ago)</small>
    </div> <!-- /.chart-diff-dimension -->
  </div> <!-- /.chart-diff-data -->
@endif
<div id="chart-container-{{ $widget['id'] }}">
  <canvas class="chart chart-line"></canvas>
</div>

@section('widgetScripts')
<script type="text/javascript">
  // Set chart data
  var widgetData{{ $widget['id'] }} = {
    'isCombined' : @if($widget['data']['isCombined']) true @else false @endif,
    'labels': [@foreach ($widget['data']['labels'] as $datetime) "{{$datetime}}", @endforeach],
    'datasets': [
    @foreach ($widget['data']['datasets'] as $dataset)
      {
          'values' : [{{ implode(',', $dataset['values']) }}],
          'name' : "{{ $dataset['name'] }}",
          'color': "{{ $dataset['color'] }}"
      },
    @endforeach
    ]
  }
</script>
@append
