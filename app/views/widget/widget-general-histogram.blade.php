<div class="chart-data">
  <div class="chart-name">
    {{ $widget->getSettings()['name'] }}
  </div> <!-- /.chart-name -->
  <div class="chart-value">
    @if ($widget->state == 'active')
      @if ( ! $widget->hasCumulative())
      {{ Utilities::formatNumber($widget->getLatestValues()['value'], $widget->getFormat()) }}
      @else
      {{ Utilities::formatNumber($widget->getDiff($widget->getSettings()['length']), $widget->getFormat()) }}
      @endif
    @endif
  </div> <!-- /.chart-value -->
</div> <!-- /.chart-data -->

<div class="chart-diff-data text-center">

  <div class="chart-diff @if($widget->isSuccess()) text-success @else text-danger @endif">
  @if ($widget->getDiff() >= 0)
      <span class="fa fa-arrow-up chart-diff-icon"> </span>
  @else
      <span class="fa fa-arrow-down chart-diff-icon"> </span>
  @endif
    <span class="chart-diff-value">{{ Utilities::formatNumber($widget->getDiff(), $widget->getFormat()) }}</span>
  </div> <!-- /.chart-diff -->


  <div class="chart-diff-dimension">
    <small>(a {{ rtrim($widget->getSettings()['resolution'], 's') }} ago)</small>
  </div> <!-- /.chart-diff-dimension -->
</div> <!-- /.chart-diff-data -->

<div id="chart-container-{{ $widget->id }}" class="clickable">
  <canvas id="chart-{{ $widget->id }}" class="chart chart-line"></canvas>
</div>

@section('widgetScripts')
<script type="text/javascript">
  // Set chart data
  var widgetData{{ $widget->id }} = {
    'labels': [@foreach ($widget->getData()['labels'] as $datetime) "{{$datetime}}", @endforeach],
    'datasets': [
    @foreach ($widget->getData()['datasets'] as $dataset)
      {
          'values' : [{{ implode(',', $dataset['values']) }}],
          'color': "{{ $dataset['color'] }}"
      },
    @endforeach
    ]
  }
</script>
@append