@if ($widget['settings']['type'] == 'table')
  @include('widget.widget-general-table', ['title' => 'Goal completion'])
@else
  @include('widget.widget-general-histogram')
@endif
