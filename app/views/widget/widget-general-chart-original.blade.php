<div class="chart-value larger-text">
  {{ Utilities::formatNumber(array_values($widget['data'][$widget['defaultLayout']]['currentValue'])[0], $widget['format']) }}
</div> <!-- /.chart-value -->

<div class="chart-diff-data text-center">
  <div class="chart-diff @if($widget['className']::isSuccess($widget['data'][$widget['defaultLayout']]['currentDiff'])) text-success @else text-danger @endif">
  @if ($widget['data'][$widget['defaultLayout']]['currentDiff'] >= 0)
      <span class="fa fa-arrow-up chart-diff-icon"> </span>
  @else
      <span class="fa fa-arrow-down chart-diff-icon"> </span>
  @endif
    <span class="chart-diff-value larger-text">{{ Utilities::formatNumber(array_values($widget['data'][$widget['defaultLayout']]['currentDiff'])[0], $widget['format']) }}</span>
  </div> <!-- /.chart-diff -->

  <div class="chart-diff-dimension smaller-text">
    <small>(a {{ rtrim($widget['settings']['resolution'], 's') }} ago)</small> 
  </div> <!-- /.chart-diff-dimension -->
</div> <!-- /.chart-diff-data -->

<p class="chart-name text-center">
  {{ $widget['settings']['name'] }}
</p> <!-- /.chart-name -->

<div id="chart-container-{{ $widget['id'] }}">
  <canvas class="chart chart-line"></canvas>
</div>
