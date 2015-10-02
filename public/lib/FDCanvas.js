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
  var options           = widgetOptions;
  var widgetSelector    = options.selector;
  var containerSelector = options.selector + ' [id^=chart-container]';

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
    if (options.page = 'dashboard') {
      widthMargin = 20;
      heigthMargin = 35;
    } else if (options.page = 'singlestat') {
      widthMargin = 0;
      heigthMargin = 0;
    };

    // Return
    return {'width': $(widgetSelector).first().width()-widthMargin,
            'height': $(widgetSelector).first().height()-heigthMargin};
  }

  /**
   * @function get2dContext
   * --------------------------------------------------------------------------
   * Returns the canvas 2d Context
   * @return {dictionary} context | The canvas get2dContext
   * --------------------------------------------------------------------------
   */
  function get2dContext() {
    if ($(containerSelector).find('canvas').length) {
      return $(containerSelector).find('canvas')[0].getContext("2d");
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
    $(containerSelector).empty();
    // Add new canvas
    if (options.page == 'dashboard') {
      $(containerSelector).append('<canvas class="chart chart-line" height="' + canvasSize.height +'" width="' + canvasSize.width + '"></canvas>');
    } else if (options.page == 'singlestat') {
      $(containerSelector).append('<canvas class="img-responsive canvas-auto" height="' + canvasSize.height +'" width="' + canvasSize.width + '"></canvas>');
    };

    // Return
    return this;
  }

  /* -------------------------------------------------------------------------- *
   *                                  EVENTS                                    *
   * -------------------------------------------------------------------------- */
  /**
   * @event $(containerSelector).mousedown
   * --------------------------------------------------------------------------
   * Checks the click/drag moves
   * --------------------------------------------------------------------------
   */
  if (options.features.drag) {
    $(containerSelector).mousedown(function() {
      isDragging = false;
    })
  };

  /**
   * @event $(containerSelector).mousemove
   * --------------------------------------------------------------------------
   * Checks the click/drag moves
   * --------------------------------------------------------------------------
   */
  if (options.features.drag) {
    $(containerSelector).mousemove(function() {
      isDragging = true;
    })
  };

  /**
   * @event $(containerSelector).mouseup
   * --------------------------------------------------------------------------
   * Checks the click/drag moves
   * --------------------------------------------------------------------------
   */
  if (options.features.drag) {
    $(containerSelector).mouseup(function() {
      var wasDragging = isDragging;
      if (!wasDragging) {
        window.location = options.urls.statUrl;
      }
      isDragging = false;
    })
  };

} // FDCanvas
