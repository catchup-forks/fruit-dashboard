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
            {{ $widget->getSettings()['name'] }}
          </h1> <!-- /.text-center -->

          <div class="row">

            <!-- Nav tabs -->
            <div class="col-md-12 text-center">
              <ul class="nav nav-pills center-pills" role="tablist">
                @foreach ($widget->resolution() as $resolution=>$value)
                    <li role="presentation"><a href="#singlestat-{{ $resolution }}" aria-controls="singlestat-{{ $resolution }}" role="tab" data-toggle="pill" data-resolution="{{$resolution}}">{{$value}}</a></li>
                @endforeach
              </ul>
            </div> <!-- /.col-md-12 -->


            <!-- Tab panes -->
              <div class="tab-content">
                @foreach ($widget->resolution() as $resolution=>$value)
                  <div role="tabpanel" class="tab-pane fade col-md-12" id="singlestat-{{ $resolution }}">
                    {{-- Check Premium feature and disable charts if needed --}}
                    @if (!Auth::user()->subscription->getSubscriptionInfo()['PE'])
                      @include('singlestat.singlestat-premium-feature-needed')
                    @else
                      @include('singlestat.singlestat-element')
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
  <!-- FDJSlibs merged -->
  {{ Minify::javascriptDir('/lib/general') }}
  {{ Minify::javascriptDir('/lib/layouts') }}
  {{ Minify::javascriptDir('/lib/widgets') }}
  <!-- FDJSlibs merged -->

  <!-- Init FDGlobalChartOptions -->
  <script type="text/javascript">
      new FDGlobalChartOptions({data:{page: 'singlestat'}}).init();
  </script>
  <!-- /Init FDGlobalChartOptions -->

  <script type="text/javascript">
    @foreach ($widget->resolution() as $resolution=>$value)

      var widgetOptions{{ $resolution }} = {
          general: {
            id:    '{{ $widget->id }}',
            name:  '{{ $widget->name }}',
            type:  '{{ $widget->getDescriptor()->type }}',
            state: '{{ $widget->state }}',
          },
          features: {
            drag:    false,
          },
          urls: {},
          selectors: {
            widget: '#panel-{{ $resolution }}',
            graph:  '#chart-{{ $resolution }}'
          },
          data: {
            page: 'singlestat',
            init: 'widgetData{{ $resolution }}',
          },
          layout: 'chart'
      }

      var widgetData{{ $resolution }} = {
        'isCombined' : @if($widget->getData(['resolution'=>$resolution])['isCombined']) true @else false @endif,
        'labels': [@foreach ($widget->getData(['resolution'=>$resolution])['labels'] as $datetime) "{{$datetime}}", @endforeach],
        'datasets': [
        @foreach ($widget->getData(['resolution'=>$resolution])['datasets'] as $dataset)
          {
              'type' : '{{ $dataset['type'] }}',
              'values' : [{{ implode(',', $dataset['values']) }}],
              'name':  "{{ $dataset['name'] }}",
              'color': "{{ $dataset['color'] }}"
          },
        @endforeach
        ]
      }
    @endforeach

    $(document).ready(function () {
      // Show first tab
      //$('.nav-pills a:first').tab('show');
      // Show weeks Tab
      $('.nav-pills a').each(function(index,element){
        if($(element).data('resolution')=='{{ $widget->getSettings()["resolution"] }}') {
          $(element).tab('show');
        }
      });

      // Create graph objects
      @foreach ($widget->resolution() as $resolution=>$value)
        FDWidget{{ $resolution }} = new window['FD{{ Utilities::underscoreToCamelCase($widget->getDescriptor()->type)}}Widget'](widgetOptions{{ $resolution }});
      @endforeach

      // Show graph on change
      $('a[data-toggle="pill"]').on('shown.bs.tab', function (e) {
        window['FDWidget' + $(e.target).data('resolution')].reinit();
      });
    });
  </script>
  @append
