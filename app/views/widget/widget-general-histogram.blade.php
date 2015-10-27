@if ($widget['layout'] == 'table')
  @include('widget.widget-general-table')
@elseif ($widget['layout'] == 'count')
  @include('widget.widget-general-count')
@else
  @include('widget.widget-general-chart')
@endif
