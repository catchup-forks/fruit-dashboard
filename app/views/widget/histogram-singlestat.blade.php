@extends('meta.base-user')

@section('pageTitle')
Widget stats
@stop

@section('pageStylesheet')
@stop

@section('pageContent')
<div class="container">

  <h1 class="text-center text-white drop-shadow">
    {{ $widget->descriptor->name }}
  </h1>

  <div class="row">
    <div class="col-md-10 col-md-offset-1">
    @foreach ($frequencies as $frequency)
      <div class="col-md-6">
        <div class="panel fill panel-default panel-transparent">
          <div class="panel-heading">
            <div class="panel-title">
              @if (($widget->getSettings()['frequency'] == $frequency) && ($widget->state != 'hidden'))
              <span
               class="drop-shadow z-top pull-right"
               data-toggle="tooltip"
               data-placement="left"
               title="This chart is currently pinned to the dashboard">
               <span class="label label-success label-as-badge valign-middle">
                <span class="icon fa fa-tag">
                </span>
                </span>
              </span>
              @else
              <a href="{{ route('widget.pin-to-dashboard', array($widget->id, $frequency)) }}"
               class="drop-shadow z-top no-underline pull-right"
               data-toggle="tooltip"
               data-placement="left"
               title="Pin this chart to the dashboard">
               <span class="label label-info label-as-badge valign-middle">
                 <span class="icon fa fa-thumb-tack">
                 </span>
               </span>
              </a>
              @endif
              {{ $frequency }} statistics
            </div>
          </div>
          <div class="panel-body no-padding" id="chart-container-{{$frequency}}">

            <canvas id="chart-{{$frequency}}"></canvas>
          </div> <!-- /.panel-body -->
        </div> <!-- /.panel -->
      </div> <!-- /.col-md-6 -->
    @endforeach
    </div> <!-- /.col-md-10 -->
  </div> <!-- /.container -->

  @stop

  @section('pageScripts')

  @include('widget.widget-general-scripts')
  <script type="text/javascript">
    $(document).ready(function () {
      var frequencies = ['daily', 'weekly', 'monthly', 'yearly'];

      function loadStat(i) {
        if (i >= frequencies.length) {
          return;
        }
        var postData = {
          'frequency': frequencies[i],
          'state_query': true
        };
        var canvas = $("#chart-" + frequencies[i]);
        sendAjax(postData, {{ $widget->id }}, function (data) {
          updateChartWidget(data['data'], canvas, frequencies[i]);
          // Recursive call.
          loadStat(++i);
        });
      }

      // Chart loading initial call.
      loadStat(0);
    });
  </script>

  @append
