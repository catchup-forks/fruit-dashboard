<script type="text/javascript">
// Set options
var gridsterGlobalOptions = {
  numberOfCols:     {{ SiteConstants::getGridNumberOfCols() }},
  numberOfRows:     {{ SiteConstants::getGridNumberOfRows() }},
  widgetMargin:     {{ SiteConstants::getWidgetMargin() }},
  widgetWidth:     ($('.grid-base').width() / {{ SiteConstants::getGridNumberOfCols() }}) - ({{ SiteConstants::getWidgetMargin() }} * 2),
  widgetHeight:    ($('.grid-base').height() / {{ SiteConstants::getGridNumberOfRows() }}) - ({{ SiteConstants::getWidgetMargin() }} * 2),
  saveUrl:          "{{ route('widget.save-position') }}",
  postUrl:          "{{ route('widget.save-position') }}",
  lockIconSelector: "#dashboard-lock",
};

// Create FDGridster objects
var gridsterOptions{{ $dashboard['id'] }} = $.extend({},
gridsterGlobalOptions,
{
  id:   '{{ $dashboard["id"] }}',
  name: '{{ $dashboard["name"] }}',
  namespace:        '#gridster-{{ $dashboard["id"] }}',
  gridsterSelector: 'div.gridster-container',
  widgetsSelector:  'div.gridster-widget',
  isLocked:  {{ $dashboard["is_locked"] }},
  lockUrl:        "{{ route('dashboard.lock', $dashboard['id']) }}",
  unlockUrl:      "{{ route('dashboard.unlock', $dashboard['id']) }}",
  setVelocityUrl: "{{ route('dashboard.set-velocity', $dashboard['id']) }}",
}
);
var widgetsOptions{{ $dashboard['id'] }} = [
@foreach ($dashboard['widgets'] as $widget) {{ json_encode($widget['meta']) }}, @endforeach
];

var FDGridster{{ $dashboard['id'] }} = new FDGridster(gridsterOptions{{ $dashboard['id'] }});


// Initialize FDGridster objects on DOM load
$(document).ready(function() {
  FDGridster{{ $dashboard['id'] }}.init().build(widgetsOptions{{ $dashboard['id'] }});
});

// Fade in the current gridster
$('.gridster.not-visible').fadeIn(1300);

</script>
