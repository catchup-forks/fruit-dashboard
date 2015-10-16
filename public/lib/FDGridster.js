/**
 * @class FDGridster
 * --------------------------------------------------------------------------
 * Class function for the gridster elements
 * --------------------------------------------------------------------------
 */
function FDGridster(gridsterOptions) {
 /* -------------------------------------------------------------------------- *
  *                                 ATTRIBUTES                                 *
  * -------------------------------------------------------------------------- */
  // Global
  var options  = gridsterOptions;
  // Gridster related
  var gridster = null;
  // Widgets related
  var widgets  = [];

  // Public functions
  this.init   = init;
  this.build  = build;
  this.lock   = lock;
  this.unlock = unlock;

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
  function build(widgetsOptions) {
    // Build widgets
    for (var i = widgetsOptions.length - 1; i >= 0; i--) {
      // Add parent selector to widget selector
      widgetsOptions[i].selectors.widget =
        options.namespace + ' ' +
        options.widgetsSelector +
        widgetsOptions[i].selectors.widget;
      // Initialize widget
      var widget = new FDWidget(widgetsOptions[i]);
      // Poll state from js if the wiget is loading
      if (widgetsOptions[i].general.state == 'loading') {
        widget.load();
      };
      // Add to widgets array
      widgets.push({'id': widgetsOptions[i].general.id, 'widget': widget});
    };

    // Look for off-screen widgets on dashboard.
    getOverflow(widgetsOptions);

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
    gridOptions = $.extend({},
                  getDefaultOptions(),
                  {resize:    getResizeOptions()},
                  {draggable: getDraggingOptions()}
              );

    // Create gridster.js object and lock / unlock
    if (options.isLocked) {
      gridster = $(options.namespace + ' ' + options.gridsterSelector).gridster(gridOptions).data('gridster').disable();
      lock();
    } else {
      gridster = $(options.namespace + ' ' + options.gridsterSelector).gridster(gridOptions).data('gridster');
      unlock();
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
      /* Trimming the id from the selector. */
      var selector = widgets[i].widget.getSelector();
      var id = selector.substring(
        selector.lastIndexOf("=") + 1,
        selector.lastIndexOf("]")
      );
      if (widgetId == id) {
        widget = widgets.splice(i, 1)[0].widget;
        break;
      };
    };

    if (widget != null) {
      // Remove element from the gridster
      gridster.remove_widget(widget.getSelector());
      // Delete widget
      widget.remove()
    };

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
    var hoverableSelector = options.namespace + ' *[data-hover="hover-unlocked"]';
    if (isLocked) {
      $.each($(hoverableSelector), function(){
        $(this).children(":first").css('display', 'none');
      });
      $(options.widgetsSelector).removeClass('can-hover');
    } else {
      $.each($(hoverableSelector), function(){
        $(this).children(":first").css('display', '');
      });
      $(options.widgetsSelector).addClass('can-hover');
    };

  }


  /**
   * @function lock
   * --------------------------------------------------------------------------
   * Locks the actual gridster object
   * @return {null} None
   * --------------------------------------------------------------------------
   */
  function lock() {
      // Disable resize
      gridster.disable_resize();
      // Disable gridster movement
      gridster.disable();
      // Hide hoverable elements.
      handleHover(true);
  }

  /**
   * @function unlock
   * --------------------------------------------------------------------------
   * Unlocks the actual gridster object
   * @return {null} None
   * --------------------------------------------------------------------------
   */
  function unlock() {
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
  function getDefaultOptions() {
    // Build options dictionary
    defaultOptions = {
      namespace:                options.namespace,
      widget_selector:          options.widgetsSelector,
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
  function getResizeOptions() {
    // Build options dictionary
    resizeOptions = {
      enabled: true,
      start: function() {
        $(options.widgetsSelector).toggleClass('hovered');
      },
      stop: function(e, ui, $widget) {
        $.ajax({
          type: "POST",
          data: {'positioning': serializePositioning()},
          url: options.saveUrl
         });
        $(options.widgetsSelector).toggleClass('hovered');
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
        $(options.widgetsSelector).toggleClass('hovered');
      },
      stop: function(e, ui, $widget) {
        $.ajax({
          type: "POST",
          data: {'positioning': serializePositioning()},
          url: options.saveUrl
        });
        $(options.widgetsSelector).toggleClass('hovered');
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

  /**
   * @function getOverflow
   * --------------------------------------------------------------------------
   * Displays a growl notification if there are off-screen widgets
   * @param {array} widgetsOptions | The options array for the widgets of a dashboard
   * @return this
   * --------------------------------------------------------------------------
   */
  function getOverflow(widgetsOptions) {
    var lowestRow = 0;

    for (var i = widgetsOptions.length - 1; i >= 0; i--) {
      var localRowMax = parseInt(widgetsOptions[i].general.row) + parseInt(widgetsOptions[i].general.sizey) - 1;
      if (localRowMax > lowestRow) {
        lowestRow = localRowMax;
      }
    };

    if (lowestRow > options.numberOfRows) {
      var msg = "There is a off-screen widget on your dashboard: " + options.name + ".";
      easyGrowl('warning', msg, 10000);
    };

    return this;
  }

  /* -------------------------------------------------------------------------- *
   *                                   EVENTS                                   *
   * -------------------------------------------------------------------------- */

  /**
   * @event $(".widget-delete").click
   * --------------------------------------------------------------------------
   *
   * --------------------------------------------------------------------------
   */
  $(".widget-delete").click(function(e) {
    deleteWidget($(this).attr("data-id"));
  });

} // FDGridster
