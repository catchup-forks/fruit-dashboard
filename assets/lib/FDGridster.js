/**
 * @class FDGridster
 * --------------------------------------------------------------------------
 * Class function for the gridster elements
 * --------------------------------------------------------------------------
 */
function FDGridster(dashboardID) {
  // Private variables
  var namespace   = '#gridster-' + dashboardID
  var selector    = $('#gridster-' + dashboardID + ' div.gridster-container');
  var widgetSelector = 'div.gridster-player';
  var players     = $('#gridster-' + dashboardID + ' div.gridster-player');
  var gridster    = null; 

  // Public functions
  this.initialize = initialize;
  this.lockGrid   = lockGrid;
  this.unlockGrid = unlockGrid;

  /**
   * @function initialize
   * --------------------------------------------------------------------------
   * Initializes a gridster JS object
   * @param {boolean} isLocked | true if the grid is locked, false if it isn't
   * @return {gridster.js element} gridster | The initializes gridster.js element
   * --------------------------------------------------------------------------
   */
  function initialize(isLocked, options) {
    // Build options
    options = $.extend({}, 
                  getDefaultOptions(options),
                  {resize:    getResizeOptions(options)}, 
                  {draggable: getDraggingOptions(options)}
              );
    
    // Create gridster.js object and lock / unlock
    if (isLocked) {
      gridster = selector.gridster(options).data('gridster').disable();
      lockGrid();
    } else {
      gridster = selector.gridster(options).data('gridster');
      unlockGrid();
    };
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
      gridster.disable_resize();
           
      // Disable gridster movement
      gridster.disable();

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
      gridster.enable_resize();

      // Enable gridster movement
      gridster.enable();

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
  function getDefaultOptions(options) {
    // Build options dictionary
    defaultOptions = {
      namespace:                namespace,
      widget_selector:          widgetSelector,
      widget_base_dimensions:   [options['widget_width'], options['widget_height']],
      widget_margins:           [options['widgetMargin'], options['widgetMargin']],
      min_cols:                 options['numberOfCols'],
      min_rows:                 options['numberOfRows'],
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
  function getResizeOptions(options) {
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
          url: options['saveUrl']
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
  function getDraggingOptions(options) {
    // Build options dictionary
    draggingOptions = {
      start: function() {
        players.toggleClass('hovered');
      },
      stop: function(e, ui, $widget) {
        $.ajax({
          type: "POST",
          data: {'positioning': serializePositioning()},
          url: options['saveUrl']
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
    return JSON.stringify(gridster.serialize());
  }

} // FDGridster
