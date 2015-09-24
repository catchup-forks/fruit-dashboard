@extends('meta.base-user')

@section('pageTitle')
  Dashboard
@stop

@section('pageStylesheet')
@stop

@section('pageContent')
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

    <div id="{{ $widget->id }}-chart-container">
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
          // Active widget.
          var labels =  [@foreach ($widget->getData() as $histogramEntry) "{{$histogramEntry['datetime']}}", @endforeach];
          var values = [@foreach ($widget->getData() as $histogramEntry) {{$histogramEntry['value']}}, @endforeach];

          // Removing the canvas and redrawing for proper sizing.
          canvas = reinsertCanvas(canvas);

          // Calling drawer
          drawLineGraph(canvas, [{'values': values, 'name': 'All'}], labels, name);
      });
    </script>
@stop