<div>
  <h3 id="greeting-{{ $widget['id'] }}" class="text-white text-center drop-shadow no-margin-top has-margin-vertical-sm truncate reset-line-height">
    Good <span class="greeting"></span>@if(isset(Auth::user()->name)), {{ Auth::user()->name }}@endif!
  </h3>
</div>

@section('widgetScripts')
<script type="text/javascript">
  var widgetData{{ $widget['id'] }} = {
    timeOfTheDay: "{{ GreetingsWidget::getTimeOfTheDay() }}"
  }
</script>
@append