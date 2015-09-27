/**
 * @class FDWidget
 * --------------------------------------------------------------------------
 * Class function for the widgets
 * --------------------------------------------------------------------------
 */
function FDWidget(widgetOptions) {
  /* -------------------------------------------------------------------------- *
   *                                 ATTRIBUTES                                 *
   * -------------------------------------------------------------------------- */
  var widgetId    = widgetOptions.id;
  var widgetType  = widgetOptions.type;
  var widgetState = widgetOptions.state;
  var selector    = '.gridster-player[data-id='+ widgetId +']';

  // Public functions
  this.size = size;

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
    return {'width': $(selector).width(),
            'height': $(selector).height()};
  }

  /* -------------------------------------------------------------------------- *
   *                                   EVENTS                                   *
   * -------------------------------------------------------------------------- */

  /**
   * @event $(document).ready
   * --------------------------------------------------------------------------
   * 
   * --------------------------------------------------------------------------
   */
  $(document).ready(function() {
    //console.log(size());
  });

  /**
   * @event $(selector).resize
   * --------------------------------------------------------------------------
   * Class function for the widgets
   * --------------------------------------------------------------------------
   */
  $(selector).resize(function() {
    //console.log(size());
  });


} // FDWidget

