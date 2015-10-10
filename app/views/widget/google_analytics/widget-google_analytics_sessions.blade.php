@if ($widget['settings']['type'] == 'table')
  @include('widget.widget-general-table', ['title' => 'Analytics sessions'])
@else
  @include('widget.widget-general-histogram')
@endif
