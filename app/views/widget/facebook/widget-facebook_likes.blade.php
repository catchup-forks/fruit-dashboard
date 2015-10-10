@if ($widget['settings']['type'] == 'table')
  @include('widget.widget-general-table', ['title' => 'Facebook likes'])
@else
  @include('widget.widget-general-histogram')
@endif
