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
@foreach ($dashboards as $dashboard_id => $dashboard)
  var gridsterOptions{{ $dashboard_id }} = $.extend({},
    gridsterGlobalOptions,
    {
      id:        '{{ $dashboard_id }}',
      isLocked:  {{ $dashboard["is_locked"] }},
      namespace: '#gridster-{{ $dashboard_id }}',
      name:       '{{ $dashboard["name"] }}',
      gridsterSelector: 'div.gridster-container',
      widgetsSelector:  'div.gridster-widget',
    }
  );
  var widgetsOptions{{ $dashboard_id }} = [
    @foreach ($dashboard['widgets'] as $widget) {{ json_encode($widget['meta']) }}, @endforeach
  ];

  var FDGridster{{ $dashboard_id }} = new FDGridster(gridsterOptions{{ $dashboard_id }});
@endforeach


// Initialize FDGridster objects on DOM load
$(document).ready(function() {
  @foreach ($dashboards as $dashboard_id=>$dashboard)
    FDGridster{{ $dashboard_id }}.init().build(widgetsOptions{{ $dashboard_id }});
  @endforeach
});

// Fade in the current gridster
$('.gridster.not-visible').fadeIn(1300);

</script>