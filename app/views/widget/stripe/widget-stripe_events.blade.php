<div class="panel-transparent">
  Events:<br>
  <div class="panel-body">
    @if ($widget->state == 'missing_data')
      Data not present yet!
    @else
      @foreach ($widget->getData() as $event)
        @if ($event['type'] == 'charge.succeeded')
        <span class="label label-warning label-as-badge">Charge</span>
        <span class="label label-success label-as-badge">Charge</span>
        <span class="label label-primary label-as-badge">Charge</span>
        <span class="label label-info label-as-badge">Charge</span>
        <span class="label label-danger label-as-badge">Charge</span>
          {{ $event['data']['object']['amount'] }}
          {{ $event['data']['object']['currency'] }}
        @endif
      @endforeach
    @endif
  </div>
</div>

@section('widgetScripts')

 <script type="text/javascript">
  $(document).ready(function() {
    function updateWidget() {
      $.ajax({
       type: "POST",
       data: {'type': 'charge', 'collect': false},
       url: "{{ route('widget.ajax-handler', $widget->id) }}"
      }).done(function( events ) {
        console.log(events);
        for (i = 0; i < events.length; i++) {

          console.log(events[i]);
        }

      });
    }

    $("#refresh-{{$widget->id}}").click(function () {
      console.log("loading");
      updateWidget();
    });
  });
 </script>

@append