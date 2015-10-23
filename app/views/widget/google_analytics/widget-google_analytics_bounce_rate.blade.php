@if ($widget['settings']['type'] == 'table')
  @include('widget.widget-general-table', ['title' => 'Bounce rate'])
@else
  @include('widget.widget-general-histogram')
@endif
