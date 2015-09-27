/**
 * @class FDGridster
 * --------------------------------------------------------------------------
 * Class function for the gridster elements
 * --------------------------------------------------------------------------
 */
function FDGridster(gridsterOptions, widgetsData) {
 /* -------------------------------------------------------------------------- *
  *                                 ATTRIBUTES                                 *
  * -------------------------------------------------------------------------- */
  // Private variables
  var namespace = '#gridster-' + gridsterOptions.dashboardId
  
  // Gridster related
  var gridsterOptions  = gridsterOptions;
  var gridsterSelector = namespace + ' div.gridster-container';
  var gridster         = null;
  
  // Widgets related
  var widgetsData     = widgetsData;
  var widgetsSelector = namespace + 'div.gridster-player';
  var widgets         = [];

  // Public functions
  this.init       = init;
  this.build      = build;
  this.lockGrid   = lockGrid;
  this.unlockGrid = unlockGrid;

  /* -------------------------------------------------------------------------- *
   *                                 FUNCTIONS                                  *
   * -------------------------------------------------------------------------- */

  /**
   * @function build
   * --------------------------------------------------------------------------
   * Builds the widget objects from the widget data
   * @return {this}
   * --------------------------------------------------------------------------
   */
  function build() {
    // Build widgets
    for (var i = widgetsData.length - 1; i >= 0; i--) {
      // Initialize widget
      var widget = new FDWidget(widgetsData[i]);
      // Load widget
      widget.load();
      // Add to widgets array
      widgets.push({'id': widgetsData[i].id, 'widget': widget});
    };
        
    // return
    return this;
  }


  /**
   * @function init
   * --------------------------------------------------------------------------
   * Initializes a gridster JS object
   * @return {this}
   * --------------------------------------------------------------------------
   */
  function init() {
    // Build options
    options = $.extend({}, 
                  getDefaultOptions(gridsterOptions),
                  {resize:    getResizeOptions(gridsterOptions)}, 
                  {draggable: getDraggingOptions(gridsterOptions)}
              );
    
    // Create gridster.js object and lock / unlock
    if (gridsterOptions.isLocked) {
      gridster = $(gridsterSelector).gridster(options).data('gridster').disable();
      lockGrid();
    } else {
      gridster = $(gridsterSelector).gridster(options).data('gridster');
      unlockGrid();
    };

    // Return
    return this;
  }

  /**
   * @function deleteWidget
   * --------------------------------------------------------------------------
   * Removes a widget from the grid
   * @param {integer} widgetId | The id of the widget
   * @return {this}
   * --------------------------------------------------------------------------
   */
  function deleteWidget(widgetId) {
    var widget = null;

    // Remove the FDWidget object
    for (var i = widgets.length - 1; i >= 0; i--) {
      if (widgetId == widgets[i].id) {
        widget = widgets.splice(i, 1)[0].widget;
        break;
      };
    };

    console.log(widget);
    // Remove element from the gridster
    gridster.remove_widget(widget.getSelector());

    // Signal deletion to server
    $.ajax({
      type: "POST",
      dataType: 'json',
      url: widget.getDeleteUrl(),
      data: null,
      success: function(data) {
        easyGrowl('success', "You successfully deleted the widget", 3000);
      },
      error: function(){
        easyGrowl('error', "Something went wrong, we couldn't delete your widget. Please try again.", 3000);
      }
    });

    // return
    return this;
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
      $(widgetsSelector).removeClass('can-hover');
    } else {
      $.each(hoverableElements, function(){
        $(this).children(":first").css('display', '');
      });
      $(widgetsSelector).addClass('can-hover');
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
      widget_selector:          widgetsSelector.replace(namespace,''),
      widget_base_dimensions:   [options.widget_width, options.widget_height],
      widget_margins:           [options.widgetMargin, options.widgetMargin],
      min_cols:                 options.numberOfCols,
      min_rows:                 options.numberOfRows,
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
        $(widgetsSelector).toggleClass('hovered');
      },
      stop: function(e, ui, $widget) {
        $.ajax({
          type: "POST",
          data: {'positioning': serializePositioning()},
          url: options.saveUrl
         });
        $(widgetsSelector).toggleClass('hovered');
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
        $(widgetsSelector).toggleClass('hovered');
      },
      stop: function(e, ui, $widget) {
        $.ajax({
          type: "POST",
          data: {'positioning': serializePositioning()},
          url: options.saveUrl
        });
        $(widgetsSelector).toggleClass('hovered');
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

  /* -------------------------------------------------------------------------- *
   *                                   EVENTS                                   *
   * -------------------------------------------------------------------------- */

  /**
   * @event $(".deleteWidget").click
   * --------------------------------------------------------------------------
   * 
   * --------------------------------------------------------------------------
   */
  $(".deleteWidget-" + gridsterOptions.dashboardId).click(function(e) {
    deleteWidget($(this).attr("data-id"));
  });

} // FDGridster
