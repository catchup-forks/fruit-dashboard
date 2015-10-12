@if ($widget['settings']['type'] == 'table')
  @include('widget.widget-general-table', ['title' => 'Facebook page impressions'])
@else
  @include('widget.widget-general-histogram')
@endif