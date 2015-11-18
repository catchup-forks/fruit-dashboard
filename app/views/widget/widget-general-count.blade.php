<div id="count-{{ $widget['id'] }}" class="flex-child text-center">
  <h3 class="text-white drop-shadow truncate">
    {{ Utilities::formatNumber(end($widget['data'][$layout])['value'], $widget['format']) }}
  </h3>
</div>

@section('widgetScripts')
@append
