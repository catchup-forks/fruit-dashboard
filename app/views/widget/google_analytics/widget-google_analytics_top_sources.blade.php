 @include(
  'widget.widget-general-table', [
    'widget' => $widget,
    'title' => $widget->getSettings()['name'] . '<small> (' . $widget->getSettings()['range_start'] . ' - ' . $widget->getSettings()['range_end'] . ')</small>'
  ]
)