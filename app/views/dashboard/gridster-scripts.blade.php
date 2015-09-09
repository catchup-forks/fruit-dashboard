<script type="text/javascript">

/**
 * @global 
 * --------------------------------------------------------------------------
 * Global measures
 * --------------------------------------------------------------------------
 */
var containerWidth  = $('.active > .grid-base').width();
var containerHeight = $('.active > .grid-base').height();
var numberOfCols    = {{ SiteConstants::getGridNumberOfCols() }};
var numberOfRows    = {{ SiteConstants::getGridNumberOfRows() }};
var widgetMargin    = {{ SiteConstants::getWidgetMargin() }};
var widget_width    = (containerWidth / numberOfCols) - (widgetMargin * 2);
var widget_height   = (containerHeight / numberOfRows) - (widgetMargin * 2);

/**
 * @class Gridster
 * --------------------------------------------------------------------------
 * Class function for the gridster elements
 * --------------------------------------------------------------------------
 */
function Gridster(dashboardID, isLocked) {
  // Private variables
  var namespace   = '#gridster-' + dashboardID
  var selector    = $('#gridster-' + dashboardID + ' ul');
  var players     = $('#gridster-' + dashboardID + ' li');

  // Public variables
  this.gridster = initialize(isLocked);

  // Public functions
  this.lockGrid = lockGrid;
  this.unlockGrid = unlockGrid;

  /**
   * @function initialize
   * --------------------------------------------------------------------------
   * Initializes a gridster JS object
   * @param {boolean} isLocked | true if the grid is locked, false if it isn't
   * @return {gridster.js element} gridster | The initializes gridster.js element
   * --------------------------------------------------------------------------
   */
  function initialize(isLocked) {
    if (isLocked) {
      // Build options
      options = $.extend({}, 
                    getDefaultOptions()
                );

      // Create gridster.js object
      gridster = selector.gridster(options).data('gridster').disable();

    } else {
      // Build options
      options = $.extend({}, 
                    getDefaultOptions(), 
                    {resize:    getResizeOptions()}, 
                    {draggable: getDraggingOptions()}
                );
      // Create gridster.js object
      gridster = selector.gridster(options).data('gridster');

    };

    // Handle hover elements.
    handleHover(isLocked);

    // Return
    return gridster;
  }

  /**
   * @function handleHover
   * --------------------------------------------------------------------------
   * Handles the hover display based on locking.
   * @return {null} None
   * --------------------------------------------------------------------------
   */
  function handleHover(isLocked) {
    var hoverableElements = $(namespace + " *[data-hover='hover-unlocked']");

    if (isLocked) {
      $.each(hoverableElements, function(){
        $(this).children(":first").css('display', 'none');
      });
      players.removeClass('can-hover');
    } else {
      $.each(hoverableElements, function(){
        $(this).children(":first").css('display', '');
      });
      players.addClass('can-hover');
    };
    
  }


  /**
   * @function lockGrid
   * --------------------------------------------------------------------------
   * Locks the actual gridster object
   * @return {null} None
   * --------------------------------------------------------------------------
   */
  function lockGrid() {
      // Disable resize
      this.gridster.disable_resize();
           
      // Disable gridster movement
      this.gridster.disable();

      // Hide hoverable elements.
      handleHover(true);
  }

  /**
   * @function unlockGrid
   * --------------------------------------------------------------------------
   * Unlocks the actual gridster object
   * @return {null} None
   * --------------------------------------------------------------------------
   */
  function unlockGrid() {
      // Enable resize
      this.gridster.enable_resize();

      // Enable gridster movement
      this.gridster.enable();

      // Show hoverable elements.
      handleHover(false);
  }

  /**
   * @function getDefaultOptions
   * --------------------------------------------------------------------------
   * Returns the gridster default options
   * @return {dictionary} defaultOptions | Dictionary with the options
   * --------------------------------------------------------------------------
   */
  function getDefaultOptions() {
    // Build options dictionary
    defaultOptions = {
      namespace:                namespace,
      widget_base_dimensions:   [widget_width, widget_height],
      widget_margins:           [widgetMargin, widgetMargin],
      min_cols:                 numberOfCols,
      min_rows:                 numberOfRows,
      snap_up:                  false,
      serialize_params: function ($w, wgd) {
        return {
          id: $w.data().id,
          col: wgd.col,
          row: wgd.row,
          size_x: wgd.size_x,
          size_y: wgd.size_y,
        };
      },
    }

    // Return
    return defaultOptions;
  }

  /**
   * @function getResizeOptions
   * --------------------------------------------------------------------------
   * Returns the gridster resize options
   * @return {dictionary} resizeOptions | Dictionary with the options
   * --------------------------------------------------------------------------
   */
  function getResizeOptions() {
    // Build options dictionary
    resizeOptions = {
      enabled: true,
      start: function() {
        players.toggleClass('hovered');
      },
      stop: function(e, ui, $widget) {
        $.ajax({
          type: "POST",
          data: {'positioning': serializePositioning()},
          url: "{{ route('widget.save-position') }}"
         });
        players.toggleClass('hovered');
      }
    }

    // Return
    return resizeOptions;
  }

  /**
   * @function getDraggingOptions
   * --------------------------------------------------------------------------
   * Returns the gridster dragging options
   * @return {dictionary} draggingOptions | Dictionary with the options
   * --------------------------------------------------------------------------
   */
  function getDraggingOptions() {
    // Build options dictionary
    draggingOptions = {
      start: function() {
        players.toggleClass('hovered');
      },
      stop: function(e, ui, $widget) {
        $.ajax({
          type: "POST",
          data: {'positioning': serializePositioning()},
          url: "{{ route('widget.save-position') }}"
        });
        players.toggleClass('hovered');
       }
    }

    // Return
    return draggingOptions;
  }

  /**
   * @function serializePositioning
   * --------------------------------------------------------------------------
   * Serializes the gridster.js object
   * @return {json} The serialized gridster.js object
   * --------------------------------------------------------------------------
   */
  function serializePositioning() {
    return JSON.stringify(this.gridster.serialize());
  }

} // Gridster


/**
 * @global 
 * --------------------------------------------------------------------------
 * Create Gridster instances
 * --------------------------------------------------------------------------
 */
// Iterate through the dashboards, create gridster objects
@foreach (Auth::user()->dashboards as $dashboard)
  var Gridster{{ $dashboard->id }} = new Gridster({{ $dashboard->id }}, {{ $dashboard->is_locked }});
@endforeach

// Fade in the current gridster
$('.gridster.not-visible').fadeIn(1300);

</script>