/**
 * @class FDHistogramWidget
 * --------------------------------------------------------------------------
 * Class function for the Histogram Widgets
 * --------------------------------------------------------------------------
 */
var FDHistogramWidget = function(widgetOptions) {
  /* -------------------------------------------------------------------------- *
   *                                 ATTRIBUTES                                 *
   * -------------------------------------------------------------------------- */
  this.options = widgetOptions;
  this.chart   = new FDChart(widgetOptions);

  // AutoLoad
  this.init();
}

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
FDHistogramWidget.prototype.init = function() {
   this.chart.updateData(window['widgetData' + this.options.id]);
   this.chart.draw('line');
   return this;
};

/**
  * @function reinit
  * Reinitializes the widget
  * --------------------------------------------------------------------------
  * @return {this} 
  * --------------------------------------------------------------------------
  */
FDHistogramWidget.prototype.reinit = function() {
   this.chart.draw('line');
   return this;
};

/**
 * @function refresh
 * Handles the specific refresh procedure to the widget
 * --------------------------------------------------------------------------
 * @return {this} 
 * --------------------------------------------------------------------------
 */
FDHistogramWidget.prototype.refresh = function(data) {
  this.chart.updateData(data);
  this.chart.draw('line');
  return this;
}

/* -------------------------------------------------------------------------- *
 *                                   EVENTS                                   *
 * -------------------------------------------------------------------------- */
