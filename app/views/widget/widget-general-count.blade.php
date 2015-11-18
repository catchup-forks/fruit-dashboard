<div id="count-{{ $widget['id'] }}" class="flex-child">
  <div class="count-value">
    <span>{{ Utilities::formatNumber(end($widget['data'][$layout])['value'], $widget['format']) }}</span>  
  </div> <!-- /.count-value -->
</div>

@section('widgetScripts')
@append
