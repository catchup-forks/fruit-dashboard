<div id="count-{{ $widget['id'] }}" class="flex-child">
  <div class="count-value text-center">
    <span>{{ Utilities::formatNumber(end($widget['data'][$layout])['value'], $widget['format']) }}</span>  
  </div> <!-- /.count-value -->
</div>
<div class="count-title text-center">
  {{$widget['name']}}
</div> <!-- /.count-title -->

@section('widgetScripts')
@append
