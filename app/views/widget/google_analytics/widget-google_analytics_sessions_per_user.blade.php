@if ($widget['settings']['type'] == 'table')
  @include('widget.widget-general-table', ['title' => 'Sessions/user'])
@else
  @include('widget.widget-general-histogram')
@endif
