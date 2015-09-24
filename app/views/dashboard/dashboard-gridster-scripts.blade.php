<script type="text/javascript">

/**
 * --------------------------------------------------------------------------
 * Create Gridster instances
 * --------------------------------------------------------------------------
 */
// Set options
var gridsterOptions = {
  'numberOfCols'  : {{ SiteConstants::getGridNumberOfCols() }},
  'numberOfRows'  : {{ SiteConstants::getGridNumberOfRows() }},
  'widgetMargin'  : {{ SiteConstants::getWidgetMargin() }},
  'widget_width'  : ($('.active > .grid-base').width() / {{ SiteConstants::getGridNumberOfCols() }}) - ({{ SiteConstants::getWidgetMargin() }} * 2),
  'widget_height' : ($('.active > .grid-base').height() / {{ SiteConstants::getGridNumberOfRows() }}) - ({{ SiteConstants::getWidgetMargin() }} * 2),
  'saveUrl'       : "{{ route('widget.save-position') }}"
};

// Create Gridster objects
@foreach (Auth::user()->dashboards as $dashboard)
  var Gridster{{ $dashboard->id }} = new Gridster({{ $dashboard->id }});
@endforeach

// Initialize Gridster objects on DOM load
$(document).ready(function() {
  @foreach (Auth::user()->dashboards as $dashboard)
    Gridster{{ $dashboard->id }}.initialize({{ $dashboard->is_locked }}, gridsterOptions);
  @endforeach
});

// Fade in the current gridster
$('.gridster.not-visible').fadeIn(1300);

</script>