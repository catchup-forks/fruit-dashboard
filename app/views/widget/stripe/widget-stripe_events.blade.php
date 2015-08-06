<div class="panel-transparent">
  Events:<br>
  <div class="panel-body">
    @if ($widget->state == 'missing_data')
      Data not present yet!
    @else
      @foreach ($widget->getData() as $event)
        @if ($event['type'] == 'charge.succeeded')
        Charge
          {{Carbon::createFromTimestamp($event['created'])->toDateTimeString()}}
          {{ $event['data']['object']['amount'] }}
          {{ $event['data']['object']['currency'] }}
          <br>
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
       data: {},
       url: "{{ route('widget.ajax-handler', $widget->id) }}"
      }).done(function( data ) {});
    }

    $("#refresh-{{$widget->id}}").click(function () {
      updateWidget();
    });
  });
 </script>

@append