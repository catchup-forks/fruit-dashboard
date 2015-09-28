<script type="text/javascript">

/**
 * --------------------------------------------------------------------------
 * Create FDGridster instances
 * --------------------------------------------------------------------------
 */
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
     {'dashboardId': '{{ $dashboard->id }}',
      'isLocked':    {{ $dashboard->is_locked }}}
  );
  var widgetsData{{ $dashboard->id }} = [
    @foreach ($dashboard->widgets as $widget)
      {'id':        '{{ $widget->id }}',
       'name':      '{{ $widget->name }}',
       'type':      '{{ $widget->descriptor->type }}',
       'state':     '{{ $widget->state }}',
       'postUrl':   '{{ route("widget.ajax-handler", $widget->id) }}',
       'deleteUrl': '{{ route("widget.delete", $widget->id) }}',
       'page':      'dashboard'},
    @endforeach
  ];
  var FDGridster{{ $dashboard->id }} = new FDGridster(gridsterOptions{{ $dashboard->id }});
@endforeach

// Initialize FDGridster objects on DOM load
$(document).ready(function() {
  @foreach (Auth::user()->dashboards as $dashboard)
    FDGridster{{ $dashboard->id }}.init().build(widgetsData{{ $dashboard->id }});
  @endforeach
});

// Fade in the current gridster
$('.gridster.not-visible').fadeIn(1300);

</script>