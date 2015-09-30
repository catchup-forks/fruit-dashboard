@extends('meta.base-user')

@section('pageTitle')
Widget stats
@stop

@section('pageStylesheet')
@stop

@section('pageContent')
<div class="container">
  <div class="row margin-top">
    <div class="col-md-10 col-md-offset-1">
      <div class="panel panel-default panel-transparent">
        <div class="panel-body">

          <h1 class="text-center">
            {{ $widget->descriptor->name }}
          </h1> <!-- /.text-center -->

          <div class="row">

            <!-- Nav tabs -->
            <div class="col-md-12 text-center">
              <ul class="nav nav-pills center-pills" role="tablist">
                @foreach ($widget->resolution() as $resolution=>$value)
                    <li role="presentation"><a href="#{{$value}}" aria-controls="{{$value}}" role="tab" data-toggle="pill" data-resolution="{{$resolution}}">{{$value}}</a></li>
                @endforeach
              </ul>
            </div> <!-- /.col-md-12 -->


            <!-- Tab panes -->
              <div class="tab-content">
                @foreach ($widget->resolution() as $resolution=>$value)
                  <div role="tabpanel" class="tab-pane fade col-md-12" id="{{$value}}">
                    {{-- Check Premium feature and disable charts if needed --}}
                    @if (!Auth::user()->subscription->getSubscriptionInfo()['PE'])
                      {{-- Allow the default chart, disable others --}}
                      @if ($resolution != $widget->getSettingsFields()['resolution']['default'])
                        @include('widget.widget-singlestat-premium-feature-needed')
                      @else
                        @include('widget.widget-singlestat-element')
                      @endif
                    @else
                      @include('widget.widget-singlestat-element')
                    @endif
                  </div> <!-- /.col-md-12 -->
                @endforeach
              </div> <!-- /.tab-content -->

          </div> <!-- /.row -->

          <div class="row">
            <div class="col-md-12 text-center">
              <a href="{{ URL::route('dashboard.dashboard') }}?active={{ $widget->dashboard->id }}" class="btn btn-primary">Back to your dashboard</a>
            </div> <!-- /.col-md-12 -->
          </div> <!-- /.row -->

        </div> <!-- /.panel-body -->
      </div> <!-- /.panel -->
    </div> <!-- /.col-md-10 -->

  </div> <!-- /.row -->


  @stop

  @section('pageScripts')

  <!-- FDChartOptions class -->
  <script type="text/javascript" src="{{ URL::asset('lib/FDChartOptions.js') }}"></script>
  <script type="text/javascript">
      globalChartOptions = new FDChartOptions('singleStat');
  </script>
  <!-- /FDChartOptions class -->

  <!-- FDChart class -->
  <script type="text/javascript" src="{{ URL::asset('lib/FDChart.js') }}"></script>
  <!-- /FDChart class -->

  <script type="text/javascript">
    $(document).ready(function () {
      var resolutions = [@foreach (array_keys($widget->resolution()) as $resolution) '{{$resolution}}', @endforeach];

      $('.nav-pills a:first').tab('show');
      // REINSERT CANVAS HERE

      // Draw chart
      // new FDChart(resolutions[i]).draw(chartData, chartOptions);

      $('a[data-toggle="pill"]').on('shown.bs.tab', function (e) {
        var targetCanvas = $(e.target).data('resolution') + '-chart';

        // REINSERT CANVAS HERE

        // Draw chart
        // new FDChart(resolutions[i]).draw(chartData, chartOptions);

      })

      for (var i = 0; i < resolutions.length; i++) {
        // Default values.
        var canvas = $("#" + resolutions[i] + "-chart");
        var container = $('#chart-container-' + resolutions[i]);
        var valueSpan = $("#{{ $widget->id }}-value");
        var name = "{{ $widget->descriptor->name }}";

        var chartData = {
          'labels': [@foreach ($widget->getData()['labels'] as $datetime) "{{$datetime}}", @endforeach],
           @foreach ($widget->getData()['datasets'] as $dataset)
            'datasets': [{
              'values': [{{ implode(',', $dataset['values']) }}],
              'color': '{{ $dataset['color'] }}'
            }]
           @endforeach
        }

        // Set chart options
        var chartOptions = {
          'type': 'line',
          'chartJSOptions': globalChartOptions.getLineChartOptions()
        }

        // Draw chart
        // new FDChart(resolutions[i]).draw(chartData, chartOptions);

      };

    });
  </script>

  @append
