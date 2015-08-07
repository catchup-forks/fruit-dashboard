
@if ($widget->state == 'loading')
@include('widget.widget-loading', [
'widget' => $widget,
])
@endif

<div class="@if ($widget->state == 'loading') not-visible @endif" id="widget-wrapper-{{$widget->id}}">
  <div class="text-center">
    <span class="pull-left">
    <!-- Not yet implemented
      <select id="histogram-{{$widget->id}}-select">
      <option>Day</option>
      <option>Month</option>
      <option>Year</option>
      </select>
  -->
    </span>
    <span class="text-white drop-shadow">
       {{ $widget->descriptor->name }}
    </span>
    <span class="text-white drop-shadow pull-right">
      ${{ $widget->getlatestdata() }}
    </span>
  </div>
  <canvas id="{{$widget->descriptor->type}}-chart"></canvas>


</div>

  @section('widgetScripts')
  @if ($widget->getSettings()['widget_type'] == 'chart')
  <script type="text/javascript">
    $(document).ready(function(){
      // Collecting data.
      var labels =  [@foreach ($widget->getHistogram() as $histogramEntry) "{{$histogramEntry['date']}}", @endforeach];
      var values = [@foreach ($widget->getHistogram() as $histogramEntry) {{$histogramEntry['value']}}, @endforeach];
      var canvas = $("#{{ $widget->descriptor->type }}-chart");
      var name = "{{ $widget->descriptor->name }}";


      @if (($widget->getSettings()['widget_type'] == 'chart') && ($widget->state == 'active'))
        // Active widget.
         drawLineGraph(canvas, values, labels, name, 3000);
      @endif

      @if (($widget->getSettings()['widget_type'] == 'chart') && ($widget->state == 'loading'))
        // Loading widget.
        loadWidget(
          {{$widget->id}},
          function (data) {
          // Updating chart.
          var labels = [];
          var values = [];
          for (i = 0; i < data['entries'].length; ++i) {
            labels.push(data['entries'][i]['date']);
            values.push(data['entries'][i]['value']);
          }
          drawLineGraph(canvas, values, labels, name, 3000);
      });
      @endif


      // bind fittext to a resize event
      $('#widget-wrapper-{{$widget->id}}').bind('resize', function(e){
          
          drawLineGraph(canvas, values, labels, name, 0);
      });


      $("#refresh-{{ $widget->id }}").click(function () {
       $.ajax({
         type: "POST",
         data: {},
         url: "{{ route('widget.ajax-handler', $widget->id) }}"
       }).done(function( data ) {});
      });


    });

</script>
@endif

@append