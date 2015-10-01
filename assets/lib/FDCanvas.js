/**
 * @class FDCanvas
 * --------------------------------------------------------------------------
 * Class function for the canvas
 * --------------------------------------------------------------------------
 */
function FDCanvas(selector) {
 /* -------------------------------------------------------------------------- *
  *                                 ATTRIBUTES                                 *
  * -------------------------------------------------------------------------- */
  // Private variables
  var widgetSelector = selector;
  var containerSelector = selector + ' [id^=chart-container]';

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
    var widthMargin = 20;
    var heigthMargin = 35;
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
    $(containerSelector).append('<canvas class="chart chart-line" height="' + canvasSize.height +'" width="' + canvasSize.width + '"></canvas>');
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
  $(containerSelector).mousedown(function() {
    isDragging = false;
  })

  /**
   * @event $(containerSelector).mousemove
   * --------------------------------------------------------------------------
   * Checks the click/drag moves
   * --------------------------------------------------------------------------
   */
  $(containerSelector).mousemove(function() {
    isDragging = true;
  })

  /**
   * @event $(containerSelector).mouseup
   * --------------------------------------------------------------------------
   * Checks the click/drag moves
   * --------------------------------------------------------------------------
   */
  $(containerSelector).mouseup(function() {
    var wasDragging = isDragging;
    if (!wasDragging) {
      window.location = widgetOptions.singleStatUrl;
    }
    isDragging = false;
  })

} // FDCanvas
