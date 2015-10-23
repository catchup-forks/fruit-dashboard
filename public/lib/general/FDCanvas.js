/**
 * @class FDCanvas
 * --------------------------------------------------------------------------
 * Class function for the canvas
 * --------------------------------------------------------------------------
 */
function FDCanvas(widgetOptions) {
 /* -------------------------------------------------------------------------- *
  *                                 ATTRIBUTES                                 *
  * -------------------------------------------------------------------------- */
  // Private variables
  var options        = widgetOptions;
  var widgetSelector = options.selectors.widget;
  var graphSelector  = options.selectors.widget + ' ' + options.selectors.graph;

  // Public functions
  this.reinsert     = reinsert;
  this.get2dContext = get2dContext;

  /* -------------------------------------------------------------------------- *
   *                                 FUNCTIONS                                  *
   * -------------------------------------------------------------------------- */

  /**
   * @function size
   * --------------------------------------------------------------------------
   * Returns the widget actual size in pixels
   * @return {dictionary} size | The widget size in pixels
   * --------------------------------------------------------------------------
   */
  function size() {
    // Set margins
    if (options.data.page == 'dashboard') {
      widthMargin = 35;
      heightMargin = 20;
    } else if (options.data.page == 'singlestat') {
      widthMargin = 0;
      heightMargin = 20;
    };

    // Return
    return {'width': $(widgetSelector).first().width()-widthMargin,
            'height': $(widgetSelector).first().height()-heightMargin};
  }

  /**
   * @function get2dContext
   * --------------------------------------------------------------------------
   * Returns the canvas 2d Context
   * @return {dictionary} context | The canvas get2dContext
   * --------------------------------------------------------------------------
   */
  function get2dContext() {
    if ($(graphSelector).find('canvas').length) {
      return $(graphSelector).find('canvas')[0].getContext("2d");
    } else {
      return false;
    };
  }

  /**
   * @function reinsert
   * --------------------------------------------------------------------------
   * Reinserts the canvas with the provided size
   * @param {dictionary} size | The width and height of the new canvas
   * @return {this}
   * --------------------------------------------------------------------------
   */
  function reinsert() {
    // Get the widget size
    canvasSize = size();
    // Delete current canvas
    $(graphSelector).empty();
    // Add new canvas
    if (options.data.page == 'dashboard') {
      $(graphSelector).append('<canvas class="chart chart-line" height="' + canvasSize.height +'" width="' + canvasSize.width + '"></canvas>');
    } else if (options.data.page == 'singlestat') {
      $(graphSelector).append('<canvas class="canvas-auto" height="' + canvasSize.height +'" width="' + canvasSize.width + '"></canvas>');
    };

    // Return
    return this;
  }

  /* -------------------------------------------------------------------------- *
   *                                  EVENTS                                    *
   * -------------------------------------------------------------------------- */
  /**
   * @event $(graphSelector).mousedown
   * --------------------------------------------------------------------------
   * Checks the click/drag moves
   * --------------------------------------------------------------------------
   */
  if (options.features.drag) {
    $(graphSelector).mousedown(function() {
      isDragging = false;
    })
  };

  /**
   * @event $(graphSelector).mousemove
   * --------------------------------------------------------------------------
   * Checks the click/drag moves
   * --------------------------------------------------------------------------
   */
  if (options.features.drag) {
    $(graphSelector).mousemove(function() {
      isDragging = true;
    })
  };

  /**
   * @event $(graphSelector).mouseup
   * --------------------------------------------------------------------------
   * Checks the click/drag moves
   * --------------------------------------------------------------------------
   */
  if (options.features.drag) {
    $(graphSelector).mouseup(function() {
      var wasDragging = isDragging;
      if (!wasDragging) {
        window.location = options.urls.statUrl;
      }
      isDragging = false;
    })
  };

} // FDCanvas
