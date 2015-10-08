<script type="text/javascript">
// Set options
var gridsterGlobalOptions = {
  'numberOfCols'  : {{ SiteConstants::getGridNumberOfCols() }},
  'numberOfRows'  : {{ SiteConstants::getGridNumberOfRows() }},
  'widgetMargin'  : {{ SiteConstants::getWidgetMargin() }},
  'widget_width'  : ($('.active > .grid-base').width() / {{ SiteConstants::getGridNumberOfCols() }}) - ({{ SiteConstants::getWidgetMargin() }} * 2),
  'widget_height' : ($('.active > .grid-base').height() / {{ SiteConstants::getGridNumberOfRows() }}) - ({{ SiteConstants::getWidgetMargin() }} * 2),
  'saveUrl'       : "{{ route('widget.save-position') }}",
  'postUrl'       : "{{ route('widget.save-position') }}",
};

// Create FDGridster objects
@foreach ($dashboards as $dashboard_id=>$dashboard)
  var gridsterOptions{{ $dashboard_id }} = $.extend({},
    gridsterGlobalOptions,
    {
      id:        '{{ $dashboard_id }}',
      isLocked:  {{ $dashboard['is_locked'] }},
      namespace: '#gridster-{{ $dashboard_id }}',
      gridsterSelector: 'div.gridster-container',
      widgetsSelector:  'div.gridster-widget',
    }
  );
  var widgetsOptions{{ $dashboard_id }} = [
    @foreach ($dashboard['widgets'] as $widget) {{ json_encode($widget['meta']) }}, @endforeach
  ];
  getOverflow(widgetsOptions{{ $dashboard_id }}, "{{$dashboard['name'] }}");
  var FDGridster{{ $dashboard_id }} = new FDGridster(gridsterOptions{{ $dashboard_id }});
@endforeach

/**
 * @function getOverflow
 * --------------------------------------------------------------------------
 * Displays a growl notification if there are off-screen widgets
 * @param {array} widgetOptions | The options array for the widgets of a dashboard
 * @return null
 * --------------------------------------------------------------------------
 */
function getOverflow(widgetOptions, dashboardName) {
  var lowestRow = 0;

  for (var i = widgetOptions.length - 1; i >= 0; i--) {

    var localRowMax = parseInt(widgetOptions[i].general.row) + parseInt(widgetOptions[i].general.sizey);

    if (localRowMax > lowestRow) {
      lowestRow = localRowMax;
    }

  };

  if (lowestRow > gridsterGlobalOptions['numberOfRows']) {
    var msg = "There is a off-screen widget on your dashboard: " + dashboardName + ".";
    $.growl.warning({
      message: msg,
      fixed: true,
      location: "br"
    });
  };

  return null;
}

// Initialize FDGridster objects on DOM load
$(document).ready(function() {
  @foreach (Auth::user()->dashboards as $dashboard)
    FDGridster{{ $dashboard->id }}.init().build(widgetsOptions{{ $dashboard->id }});
  @endforeach
});

// Fade in the current gridster
$('.gridster.not-visible').fadeIn(1300);

</script>