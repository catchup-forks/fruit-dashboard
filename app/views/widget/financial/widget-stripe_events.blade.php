<h2 class="text-white text-center">
  Events:<br>
  <span>
    @if ($widget->state == 'missing_data')
      Data not present yet!
    @else
      @foreach ($widget->getData() as $event)
       {{ $event['type'] }}
      @endforeach
    @endif
  </span>
</h2>

@section('widgetScripts')

 <script type="text/javascript">
 </script>

@append