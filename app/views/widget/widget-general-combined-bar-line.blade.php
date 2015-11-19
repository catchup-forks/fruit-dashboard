<div class="chart-value larger-text">
 {{--  {{ Utilities::formatNumber(array_values($widget['data'][$widget['defaultLayout']]['currentValue'])[0], $widget['format']) }} --}}
</div> <!-- /.chart-value -->

<p class="chart-name text-center">
  {{ $widget['name'] }}
</p> <!-- /.chart-name -->

<div id="chart-wrapper-{{ $widget['id'] }}">
  <canvas class="chart"></canvas>
</div>