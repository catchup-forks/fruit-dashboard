@if ($widget['settings']['type'] == 'table')
  @include('widget.widget-general-table', ['title' => 'Stripe MRR'])
@else
  @include('widget.widget-general-histogram')
@endif
