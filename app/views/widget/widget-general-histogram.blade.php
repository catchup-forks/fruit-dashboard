
@if ($widget->state == 'loading')
@include('widget.widget-loading', [
'widget' => $widget,
])
@endif

<div class="@if ($widget->state == 'loading') not-visible @endif" id="widget-wrapper-{{$widget->id}}" style="width:100%; height:100%">
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
  <canvas id="{{$widget->descriptor->type}}-chart" style="width:100%;height:100%"></canvas>


</div>

  @section('widgetScripts')
  @if ($widget->getSettings()['widget_type'] == 'chart')
  <script type="text/javascript">
    $(document).ready(function(){
      var {{ $widget->descriptor->type }}Data = {
        labels: [@foreach ($widget->getHistogram() as $histogramEntry) "{{$histogramEntry['date']}}", @endforeach],
        datasets: [
        {
          label: "{{ $widget->descriptor->name }}",
          fillColor : "rgba(100, 222, 100,0.2)",
          strokeColor : "rgba(100, 222, 100,1)",
          pointColor : "rgba(100, 222, 100,1)",
          pointStrokeColor : "#fff",
          pointHighlightFill : "#fff",
          pointHighlightStroke : "rgba(100, 222, 100,1)",
          data: [@foreach ($widget->getHistogram() as $histogramEntry) {{$histogramEntry['value']}}, @endforeach]
        }
        ]
      }

      var options = {
         responsive: true,
         pointHitDetectionRadius : 2,
         pointDotRadius : 3,
         bezierCurve: false,
         scaleShowVerticalLines: false,
         tooltipTemplate: "<%if (label){%><%=label %>: <%}%>$<%= value %>",
         animation: false

       };

      @if (($widget->getSettings()['widget_type'] == 'chart') && ($widget->state == 'active'))
      var {{$widget->descriptor->type}}Ctx = document.getElementById("{{ $widget->descriptor->type }}-chart").getContext("2d");
      var {{ $widget->descriptor->type }}Chart = new Chart({{ $widget->descriptor->type }}Ctx).Line({{ $widget->descriptor->type }}Data, options);
      @endif

      $("#refresh").click(function () {
       $.ajax({
         type: "POST",
         data: {},
         url: "{{ route('widget.ajax-handler', $widget->id) }}"
       }).done(function( data ) {
      });
     });

      @if (($widget->getSettings()['widget_type'] == 'chart') && ($widget->state == 'loading'))
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
          {{ $widget->descriptor->type }}Data.datasets[0].labels = labels;
          {{ $widget->descriptor->type }}Data.datasets[0].data = values;
          var {{$widget->descriptor->type}}Ctx = document.getElementById("{{ $widget->descriptor->type }}-chart").getContext("2d");
          var {{ $widget->descriptor->type }}Chart = new Chart({{ $widget->descriptor->type }}Ctx).Line({{ $widget->descriptor->type }}Data, options);
      });
      @endif


      // bind fittext to a resize event
      $('#widget-wrapper-{{$widget->id}}').bind('resize', function(e){
      });

    });

</script>
@endif

@append