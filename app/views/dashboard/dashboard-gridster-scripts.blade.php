<script type="text/javascript">

/**
 * --------------------------------------------------------------------------
 * Create FDGridster instances
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

// Create FDGridster objects
@foreach (Auth::user()->dashboards as $dashboard)
  var FDGridster{{ $dashboard->id }} = new FDGridster({{ $dashboard->id }});
@endforeach

// Initialize FDGridster objects on DOM load
$(document).ready(function() {
  @foreach (Auth::user()->dashboards as $dashboard)
    FDGridster{{ $dashboard->id }}.initialize({{ $dashboard->is_locked }}, gridsterOptions);
  @endforeach
});

// Fade in the current gridster
$('.gridster.not-visible').fadeIn(1300);

</script>