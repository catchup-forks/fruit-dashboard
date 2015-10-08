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
@foreach (Auth::user()->dashboards as $dashboard)
  var gridsterOptions{{ $dashboard->id }} = $.extend({},
    gridsterGlobalOptions,
    {
      id:        '{{ $dashboard->id }}',
      isLocked:  {{ $dashboard->is_locked }},
      namespace: '#gridster-{{ $dashboard->id }}',
      gridsterSelector: 'div.gridster-container',
      widgetsSelector:  'div.gridster-widget',
    }
  );
  var widgetsOptions{{ $dashboard->id }} = [
    @foreach ($dashboard->widgets as $widget)
      {
        general: {
          id:    '{{ $widget->id }}',
          name:  '{{ $widget->name }}',
          type:  '{{ $widget->descriptor->type }}',
          state: '{{ $widget->state }}',
          row: '{{ $widget->getPosition()->row}}',
          col: '{{ $widget->getPosition()->col}}',
          sizex: '{{ $widget->getPosition()->size_x}}',
          sizey: '{{ $widget->getPosition()->size_y}}'
        },
        features: {
          drag:    true,
        },
        urls: {
          postUrl:   '{{ route("widget.ajax-handler", $widget->id) }}',
          deleteUrl: '{{ route("widget.delete", $widget->id) }}',
          statUrl:   '{{ route("widget.singlestat", $widget->id) }}',
        },
        selectors: {
          widget:  '[data-id={{ $widget->id }}]',
          wrapper: '#widget-wrapper-{{ $widget->id }}',
          loading: '#widget-loading-{{ $widget->id }}',
          refresh: '#widget-refresh-{{ $widget->id }}',
          graph:   '[id^=chart-container]',
        },
        data: {
          page: 'dashboard',
          init: 'widgetData{{ $widget->id }}',
        }
      },
    @endforeach
  ];
  getOverflow(widgetsOptions{{ $dashboard->id }}, "{{$dashboard->name }}");
  var FDGridster{{ $dashboard->id }} = new FDGridster(gridsterOptions{{ $dashboard->id }});
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