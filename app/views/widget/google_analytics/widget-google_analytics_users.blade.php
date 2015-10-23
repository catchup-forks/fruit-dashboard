@if ($widget['settings']['type'] == 'table')
  @include('widget.widget-general-table', ['title' => 'Unique visitors'])
@else
  @include('widget.widget-general-histogram')
@endif
