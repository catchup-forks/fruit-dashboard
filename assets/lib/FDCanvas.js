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
  var options = widgetOptions;
  var containerSelector = '#chart-container-' + options.general.id;

  /* FIXME. THIS NEEDS TO BE PASSED AS AN ARGUMENT OR OPTION */
  var globalselector = '.gridster-widget[data-id='+ options.general.id +']';

  // Public functions
  this.reinsert     = reinsert;
  /* FIXME SIZE IS ONLY FOR LOGGING */
  this.size         = size;
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
    /*FIXME MARGINS*/
    return {'width': $(globalselector).first().width()-20,
            'height': $(globalselector).first().height()-35};
  }

  /**
   * @function get2dContext
   * --------------------------------------------------------------------------
   * Returns the canvas 2d Context
   * @return {dictionary} context | The canvas get2dContext
   * --------------------------------------------------------------------------
   */
  function get2dContext() {
    return $(containerSelector).find('canvas')[0].getContext("2d");
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
    $(containerSelector).append('<canvas id=chart-' + widgetOptions.id + ' class="chart chart-line" height="' + canvasSize.height +'" width="' + canvasSize.width + '"></canvas>');
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
