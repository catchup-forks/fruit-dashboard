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

        <div class="col-md-6">
          <div class="panel fill panel-default panel-transparent">
            <div class="panel-heading">
              <div class="panel-title text-center">
                Daily statistics
              </div>
            </div>
            <div class="panel-body no-padding" id="chart-container-daily">

              <canvas id="chart-daily"></canvas>
            </div> <!-- /.panel-body -->
          </div> <!-- /.panel -->
        </div> <!-- /.col-md-6 -->

        <div class="col-md-6">
          <div class="panel panel-default panel-transparent">
            <div class="panel-heading">
              <div class="panel-title text-center">
                Weekly statistics
              </div>
            </div>
            <div class="panel-body no-padding" id="chart-container-weekly">
              <canvas id="chart-weekly"></canvas>
            </div> <!-- /.panel-body -->
          </div> <!-- /.panel -->
        </div> <!-- /.col-md-6 -->

        <div class="col-md-6">
          <div class="panel panel-default panel-transparent">
            <div class="panel-heading">
              <div class="panel-title text-center">
                Monthly statistics
              </div>
            </div>
            <div class="panel-body no-padding" id="chart-container-monthly">
              <canvas id="chart-monthly"></canvas>
            </div> <!-- /.panel-body -->
          </div> <!-- /.panel -->
        </div> <!-- /.col-md-6 -->

        <div class="col-md-6">
          <div class="panel panel-default panel-transparent">
            <div class="panel-heading">
              <div class="panel-title text-center">
                Yearly statistics
              </div>
            </div>
            <div class="panel-body no-padding" id="chart-container-yearly">
              <canvas id="chart-yearly"></canvas>
            </div> <!-- /.panel-body -->
          </div> <!-- /.panel -->
        </div> <!-- /.col-md-6 -->

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
