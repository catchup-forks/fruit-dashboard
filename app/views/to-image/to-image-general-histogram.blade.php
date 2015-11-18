@extends('to-image.to-image-meta')

@section('pageContent')
<div style="height:200px;width:300px">

  <h1>Hello</h1>
  <div class="fill" id="widget-wrapper-{{$widget['general']['id']}}">
    <div class="chart-data">
    <div class="chart-name larger-text">
      {{ $widget['settings']['name'] }}
    </div> <!-- /.chart-name -->
    <div class="chart-value larger-text">
      {{ Utilities::formatNumber(array_values($widget['instance']->getLatestValues())[0], $widget['format']) }}
    </div> <!-- /.chart-value -->
  </div> <!-- /.chart-data -->
  </div>

  <div class="chart-diff-data text-center">

    <div class="chart-diff @if($widget['instance']->isSuccess($widget['defaultDiff'])) text-success @else text-danger @endif">
    @if ($widget['defaultDiff'] >= 0)
        <span class="fa fa-arrow-up chart-diff-icon"> </span>
    @else
        <span class="fa fa-arrow-down chart-diff-icon"> </span>
    @endif
      <span class="chart-diff-value larger-text">{{ Utilities::formatNumber($widget['defaultDiff'], $widget['format']) }}</span>
    </div> <!-- /.chart-diff -->


    <div class="chart-diff-dimension smaller-text">
      <small>(a {{ rtrim($widget['settings']['resolution'], 's') }} ago)</small>
    </div> <!-- /.chart-diff-dimension -->
  </div> <!-- /.chart-diff-data -->

  <div id="chart-container-{{ $widget['id'] }}">
    <canvas class="chart chart-line"></canvas>
  </div>
</div>
@stop

@section('pageScripts')
    <!-- FDJSlibs -->
    {{ Minify::javascriptDir('/lib/layouts')->withFullUrl() }}
    {{ Minify::javascriptDir('/lib/widgets')->withFullUrl() }}
    <!-- FDJSlibs -->
@append
