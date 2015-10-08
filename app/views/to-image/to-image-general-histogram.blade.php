@extends('to-image.to-image-meta')

@section('pageContent')
<div id="panel-to-image" class="panel fill panel-default" style="width:400px; height:200px">
  <div class="panel-body no-padding" id="chart-to-image">
    <div id="chart-container">
      <canvas class="img-responsive canvas-auto"></canvas>
    </div>
  </div> <!-- /.panel-body -->
</div> <!-- /.panel -->
@stop

@section('pageScripts')

<!-- FDGeneral* classes -->
<script type="text/javascript" src="{{ URL::asset('lib/FDCanvas.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('lib/FDChart.js') }}"></script>
<script type="text/javascript" src="{{ URL::asset('lib/FDChartOptions.js') }}"></script>
<!-- /FDGeneral* classes -->

<!-- FDAbstractWidget* classes -->
<script type="text/javascript" src="{{ URL::asset('lib/widgets/FDHistogramWidget.js') }}"></script>
<!-- /FDAbstractWidget* classes -->

<!-- FDWidget* classes -->
<script type="text/javascript" src="{{ URL::asset('lib/widgets/'.$widget->descriptor->category.'/FD'. Utilities::underscoreToCamelCase($widget->descriptor->type).'Widget.js') }}"></script>
<!-- /FDWidget* classes -->

<!-- Init FDChartOptions -->
<script type="text/javascript">
    new FDChartOptions({data:{page: 'dashboard'}}).init();
</script>
<!-- /Init FDChartOptions -->

<script type="text/javascript">
    var widgetOptionsToImage = {
        general: {
          id:    '{{ $widget->id }}',
          name:  '{{ $widget->name }}',
          type:  '{{ $widget->descriptor->type }}',
          state: '{{ $widget->state }}',
        },
        features: {
          drag:    false,
        },
        urls: {},
        selectors: {
          widget: '#panel-to-image',
          graph:  '#chart-to-image'
        },
        data: {
          page: 'dashboard',
          init: 'widgetDataToImage',
        }
    }

    var widgetDataToImage = {
      'labels': [@foreach ($widget->getData()['labels'] as $datetime) "{{$datetime}}", @endforeach],
      'datasets': [
      @foreach ($widget->getData()['datasets'] as $dataset)
        {
            'values' : [{{ implode(',', $dataset['values']) }}],
            'name':  "{{ $dataset['name'] }}",
            'color': "{{ $dataset['color'] }}"
        },
      @endforeach
      ]
    }

  $(document).ready(function () {
    FDWidgetToImage = new window['FD{{ Utilities::underscoreToCamelCase($widget->descriptor->type)}}Widget'](widgetOptionsToImage);
  });
</script>
@append
