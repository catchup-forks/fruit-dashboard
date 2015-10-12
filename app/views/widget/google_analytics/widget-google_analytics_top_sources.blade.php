 @include(
  'widget.widget-general-table', [
    'title' => $widget['settings']['name'] . '<small> (' . $widget['settings']['range_start'] . ' - ' . $widget['settings']['range_end'] . ')</small>'
  ]
)