/**
 * @class FDFacebookLikesWidget
 * --------------------------------------------------------------------------
 * Class function for the FacebookLikes Widget
 * --------------------------------------------------------------------------
 */
function FDFacebookLikesWidget(widgetOptions) {
  /* -------------------------------------------------------------------------- *
   *                                 ATTRIBUTES                                 *
   * -------------------------------------------------------------------------- */
  // Private variables
  var options = widgetOptions;
  var chart   = new FDChart(widgetOptions);

  // Public functions
  this.refresh = refresh;

  // AutoLoad
  init();

  /* -------------------------------------------------------------------------- *
   *                                 FUNCTIONS                                  *
   * -------------------------------------------------------------------------- */
  
  /**
    * @function init
    * Automatically initializes the widget
    * --------------------------------------------------------------------------
    * @return {this} 
    * --------------------------------------------------------------------------
    */
   function init() {
     // Draw the chart
     chart.draw(null, 'line');
     return this;
   }


  /**
   * @function refresh
   * Handles the specific refresh procedure to the widget
   * --------------------------------------------------------------------------
   * @return {this} 
   * --------------------------------------------------------------------------
   */
  function refresh(data) {
    // redraw the canvas
    console.log('FDFacebookLikesWidget.refresh');
    chart.draw(data, 'line');
    return this;
  }

  /* -------------------------------------------------------------------------- *
   *                                   EVENTS                                   *
   * -------------------------------------------------------------------------- */


} // FDFacebookLikesWidget
