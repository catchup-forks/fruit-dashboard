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
    @foreach ($widget->resolution() as $resolution=>$value)
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
    @endforeach
    </div> <!-- /.col-md-10 -->
  </div> <!-- /.row -->

  <div class="row">
    <div class="col-md-12 text-center">
      <a href="{{ URL::route('dashboard.dashboard') }}?active={{ $widget->dashboard->id }}" class="btn btn-primary">Back to your dashboard</a>
    </div> <!-- /.col-md-12 -->
  </div> <!-- /.row -->

  @stop

  @section('pageScripts')
  @include('widget.widget-general-scripts')

  <script type="text/javascript">
    $(document).ready(function () {
      var resolutions = [@foreach (array_keys($widget->resolution()) as $resolution) '{{$resolution}}', @endforeach];

      function loadStat(i, callback) {
        if (i >= resolutions.length) {
          return callback();
        }
        var postData = {
          'resolution': resolutions[i],
          'state_query': true
        };
        var canvas = $("#chart-" + resolutions[i]);
        sendAjax(postData, {{ $widget->id }}, function (data) {
          @if($widget instanceof MultipleHistogramWidget )
            updateMultipleHistogramWidget(data['data'], canvas, resolutions[i]);
          @else
            updateHistogramWidget(data['data'], canvas, resolutions[i]);
          @endif
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
