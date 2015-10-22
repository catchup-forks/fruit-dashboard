@if ($widget['settings']['type'] == 'table')
  @include('widget.widget-general-table', ['title' => 'Average sessions duration'])
@else
  @include('widget.widget-general-histogram')
@endif
