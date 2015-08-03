<div class="text-white drop-shadow">
  {{ $widget->descriptor->name }}
  <span class="pull-right">
  ${{ $widget->getLatestData() }}
  </span>
</div>
<div class="panel-transparent">
    @if ($widget->state == 'missing_data')
      Data not present yet!
    @else
      @if ($widget->getSettings()['widget_type'] == 'chart')
      <div class="panel-body">
        <canvas id="{{$widget->descriptor->type}}-chart"></canvas>
      </div>
      @else
        ${{$widget->getLatestData()}}
      @endif
    @endif
</div>

@section('widgetScripts')
  @if ($widget->getSettings()['widget_type'] == 'chart')
  <script type="text/javascript">
    var {{ $widget->descriptor->type }}Data = {
      labels: [@foreach ($widget->getHistogram() as $histogramEntry) "{{$histogramEntry['date']}}", @endforeach],
      datasets: [
        {
          label: "{{ $widget->descriptor->name }}",
          fillColor : "rgba(151,187,205,0.2)",
          strokeColor : "rgba(151,187,205,1)",
          pointColor : "rgba(151,187,205,1)",
          pointStrokeColor : "#fff",
          pointHighlightFill : "#fff",
          pointHighlightStroke : "rgba(151,187,205,1)",
          data: [@foreach ($widget->getHistogram() as $histogramEntry) {{$histogramEntry['value']}}, @endforeach]
        }
      ]
    }

$(document).ready(function(){
  @if ($widget->getSettings()['widget_type'] == 'chart')
  var {{$widget->descriptor->type}}Ctx = document.getElementById("{{ $widget->descriptor->type }}-chart").getContext("2d");
  var {{ $widget->descriptor->type }}Chart = new Chart({{ $widget->descriptor->type }}Ctx).Line({{ $widget->descriptor->type }}Data,
    {
      responsive:true,
      pointHitDetectionRadius : 2,
      pointDotRadius : 3,
    });

   $("#refresh").click(function () {
     $.ajax({
       type: "POST",
       data: {},
       url: "{{ route('widget.ajax-handler', $widget->id) }}"
      }).done(function( data ) {
        $("#quote").html(data['quote']);
        $("#author").html(data['author']);
      });
   });



  @endif
});



  </script>
  @endif

@append