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
    @foreach ($widget->frequency() as $frequency=>$value)
      {{-- Check Premium feature and disable charts if needed --}}
      @if (!Auth::user()->subscription->getSubscriptionInfo()['PE'])
        {{-- Allow the default chart, disable others --}}
        @if ($frequency != $widget->getSettingsFields()['frequency']['default'])
          @include('widget.widget-singlestat-premium-feature-needed')
        @else
          @include('widget.widget-singlestat-element')
        @endif
      @else
        @include('widget.widget-singlestat-element')
      @endif
    @endforeach
    </div> <!-- /.col-md-10 -->
  </div> <!-- /.row -->

  <div class="row">
    <div class="col-md-12 text-center">
      <a href="{{ URL::route('dashboard.dashboard') }}" class="btn btn-primary">Back to your dashboard</a>
    </div> <!-- /.col-md-12 -->
  </div> <!-- /.row -->

  @stop

  @section('pageScripts')

  @include('widget.widget-general-scripts')
  <script type="text/javascript">
    $(document).ready(function () {
      var frequencies = [@foreach (array_keys($widget->frequency()) as $frequency) '{{$frequency}}', @endforeach];

      function loadStat(i, callback) {
        if (i >= frequencies.length) {
          return callback();
        }
        var postData = {
          'frequency': frequencies[i],
          'state_query': true
        };
        var canvas = $("#chart-" + frequencies[i]);
        sendAjax(postData, {{ $widget->id }}, function (data) {
          updateChartWidget(data['data'], canvas, frequencies[i]);
          // Recursive call.
          loadStat(++i, callback);
        });
      }

      // Chart loading initial call.
      loadStat(0, function () {
        $(".chart-container").fadeIn(250);
      });
    });
  </script>

  @append
