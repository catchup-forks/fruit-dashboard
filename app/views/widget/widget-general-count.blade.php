<span class="text-white drop-shadow has-margin-horizontal" id="{{$widget->id}}-value">
  {{ var_dump($widget->getCurrentValue()) }}
</span>

@section('widgetScripts')
@append