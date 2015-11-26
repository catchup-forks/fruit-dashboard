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
  
  // Debug
  var debug = true;

  // Public functions
  this.init   = init;
  this.build  = build;
  // this.handleLock = handleLock;

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

    // Create gridster.js
    gridster = $(options.namespace + ' ' + options.gridsterSelector).gridster(gridOptions).data('gridster');

    // Handle lock based on the default options if this is the active gridster
    // if ($(options.namespace).parent().hasClass('active')) {
    //   handleLock();
    // }

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
   * @function handleLock
   * --------------------------------------------------------------------------
   * This function handles the lock / unlock process based on the gridser
   *    options.isLocked parameter
   * @param {boolean} (sendAjax) true: sends ajax update
   * @return {null} None
   * --------------------------------------------------------------------------
   */
  // function handleLock(sendAjax) {
  //   // set sendAjax false by default
  //   sendAjax = sendAjax || false;

  //   // Handle Lock icon
  //   lockIcon(options.isLocked);
    
  //   // Handle Gridster
  //   lockGridster(options.isLocked);

  //   // Handle Ajax
  //   if (sendAjax) {
  //      lockAjax(options.isLocked);
  //   }
  // }

  /**
   * @function lockIcon
   * --------------------------------------------------------------------------
   * Sets the lock parameters for a given dashboard
   * @param {boolean} (toState) true: to lock, false: to unlock
   * @return {null} None
   * --------------------------------------------------------------------------
   */
  // function lockIcon(toState) {
  //   // This is funny warlock... he-he-he
  //   var lock = $(options.lockIconSelector);
    
  //   // Set the Dashboard ID
  //   lock.attr('data-dashboard-id', options.id);
    
  //   // Hide shown tooltip.
  //   lock.tooltip('hide');

  //   // Lock
  //   if (toState) {
  //     // Set the icon
  //     lock.children('span').attr('class', 'fa fa-fw fa-lock fa-2x fa-inverse color-hovered');
  //     // Set the tooltip
  //     lock.attr('title', 'This dashboard is locked. Click to unlock.');
  //     // Set the direction
  //     lock.attr('data-lock-direction', 'unlock');

  //   // Unlock
  //   } else {
  //     // Set the icon
  //     lock.children('span').attr('class', 'fa fa-fw fa-unlock-alt fa-2x fa-inverse color-hovered');
  //     // Set the tooltip
  //     lock.attr('title', 'This dashboard is unlocked. Click to lock.');
  //     // Set the direction
  //     lock.attr('data-lock-direction', 'lock');
  //   };

  //   // Reinitialize tooltip
  //   lock.tooltip('fixTitle');  
  // }

  /**
   * @function lockGridster
   * --------------------------------------------------------------------------
   * Handles the gridster options (hover, drag, resize) based on the requested state
   * @param {boolean} (toState) true: to lock, false: to unlock
   * @return {null} None
   * --------------------------------------------------------------------------
   */
  // function lockGridster(toState) {
  //   // Lock
  //   if (toState) {
  //     // Disable resize
  //     gridster.disable_resize();
  //     // Disable gridster movement
  //     gridster.disable();
  //     // Hide hoverable elements.
  //     $(options.namespace + ' ' + options.widgetsSelector).each(function(){
  //       $(this).removeClass('can-hover');
  //     });

  //   // Unlock
  //   } else {
  //     // Enable resize
  //     gridster.enable_resize();
  //     // Enable gridster movement
  //     gridster.enable();
  //     // Show hoverable elements.
  //     $(options.namespace + ' ' + options.widgetsSelector).each(function(){
  //       $(this).addClass('can-hover');
  //     });
  //   }
  // }

  /**
   * @function lockAjax
   * --------------------------------------------------------------------------
   * Calls the locking method to save state in the database. 
   *    Reverts the whole process on fail.
   * @param {boolean} (toState) true: to lock, false: to unlock
   * @return {null} None
   * --------------------------------------------------------------------------
   */
  // function lockAjax(toState) {
  //   // Initialize variables based on the direction
  //   if (toState) {
  //     var url = options.lockUrl;
  //     var successmsg = "You successfully locked the dashboard."
  //     var errormsg = "Something went wrong, we couldn't lock your dashboard."
  //   } else {
  //     var url = options.unlockUrl;
  //     var successmsg = "You successfully unlocked the dashboard."
  //     var errormsg = "Something went wrong, we couldn't unlock your dashboard."
  //   };

  //   // Call ajax function
  //   $.ajax({
  //     type: "POST",
  //     dataType: 'json',
  //     url: url,
  //         data: null,
  //         success: function(data) {
  //           easyGrowl('success', successmsg, 3000);
  //         },
  //         error: function() {
  //           easyGrowl('error', errormsg, 3000);
  //           // Revert the process
  //           options.isLocked = !toState;
  //           handleLock();
  //         }
  //     });
  // }

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
      widget_base_dimensions:   [options.widgetWidth, options.widgetHeight],
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
  // function getOverflow(widgetsOptions) {
  //   // Initialize
  //   var lowestRow = 0;

  //   // Get the lowest row
  //   for (var i = widgetsOptions.length - 1; i >= 0; i--) {
  //     var localRowMax = parseInt(widgetsOptions[i].general.row) + parseInt(widgetsOptions[i].general.sizey) - 1;
  //     if (localRowMax > lowestRow) {
  //       lowestRow = localRowMax;
  //     }
  //   };

  //   // Send warning if there is an off screen widget
  //   if (lowestRow > options.numberOfRows) {
  //     var msg = "There is an off-screen widget on your dashboard: " + options.name + ".";
  //     easyGrowl('warning', msg, 10000);
  //   };

  //   // Return
  //   return this;
  // } 

  /* -------------------------------------------------------------------------- *
   *                                   EVENTS                                   *
   * -------------------------------------------------------------------------- */

  /**
   * @event $(".widget-delete").click
   * --------------------------------------------------------------------------
   * Handles the delete widget click
   * --------------------------------------------------------------------------
   */
  $(".widget-delete").click(function(e) {
    deleteWidget($(this).attr("data-id"));
  });

  /**
   * @event $(".granularity-button").click
   * --------------------------------------------------------------------------
   * Handles the granularity selection click event
   * --------------------------------------------------------------------------
   */
  $(".granularity-button").click(function(e) {
    if (debug) { console.log("[I] Initiated velocity change event | set all to: " + $(this).data('velocity')); };
    // Remove the active class, if any.
    $(".granularity-selector > a.active").removeClass('active');
    // Add the active class for the clicked element.
    $(this).addClass("active");
    // Change the granularity here
    $.ajax({
        type: "post",
        data: {'velocity': $(this).data('velocity')},
        url: options.setVelocityUrl
    }).done(function () {
        // Reload all widgets.
        console.log('velocity has changed');
    });
  });

  /**
   * @event $(options.lockIconSelector).click
   * --------------------------------------------------------------------------
   * Handles the click on the lock icon
   * --------------------------------------------------------------------------
   */
  // $(options.lockIconSelector).click(function() {
  //   if($(this).attr("data-dashboard-id") == options.id) {
  //     // Set isLocked
  //     options.isLocked = $(this).attr("data-lock-direction") == 'lock' ? true : false;
  //     // Handle lock process, and send ajax
  //     handleLock(true);
  //   }
  // });

  /**
   * @event $(document).ready()
   * --------------------------------------------------------------------------
   * Sets dashboard-lock icon ID to the currently active dashboard
   * --------------------------------------------------------------------------
   */
  // $(document).ready(function () {
  //   if ($(options.namespace).parent().hasClass('active')) {
  //     $(options.lockIconSelector).attr("data-dashboard-id", options.id);
  //   };
  // });

  /**
   * @event $('.carousel').on('slid.bs.carousel')
   * --------------------------------------------------------------------------
   * Change the dashboard-lock icon ID on dashboard change
   * --------------------------------------------------------------------------
   */
  // $('.carousel').on('slid.bs.carousel', function () {
  //   if ($(options.namespace).parent().hasClass('active')) {
  //     $(options.lockIconSelector).attr("data-dashboard-id", options.id);
  //     handleLock();
  //   };
  // })

} // FDGridster
