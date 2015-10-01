 @include(
  'widget.widget-general-table', [
    'widget' => $widget,
    'title' => $widget->getSettings()['name'] . '<br>(' . $widget->getSettings()['range_start'] . ' - ' . $widget->getSettings()['range_end'] . ')'
  ]
)