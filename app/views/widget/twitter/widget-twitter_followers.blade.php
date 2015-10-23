@if ($widget['settings']['type'] == 'table')
  @include('widget.widget-general-table', ['title' => 'Twitter followers'])
@else
  @include('widget.widget-general-histogram')
@endif
