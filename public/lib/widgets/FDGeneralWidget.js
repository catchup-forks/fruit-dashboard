/**
 * @class FDGeneralWidget
 * --------------------------------------------------------------------------
 * Class function for the Histogram Widgets
 * --------------------------------------------------------------------------
 */
var FDGeneralWidget = function(widgetOptions) {
  /* -------------------------------------------------------------------------- *
   *                                 ATTRIBUTES                                 *
   * -------------------------------------------------------------------------- */
  this.options    = widgetOptions;
  this.widgetData = null;
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
FDGeneralWidget.prototype.init = function() {
   this.updateData(window[this.options.data.init]);
   this.draw(this.widgetData);
   return this;
};

/**
  * @function reinit
  * Reinitializes the widget
  * --------------------------------------------------------------------------
  * @return {this} 
  * --------------------------------------------------------------------------
  */
FDGeneralWidget.prototype.reinit = function() {
   this.draw(this.widgetData);
   return this;
};

/**
 * @function refresh
 * Handles the specific refresh procedure to the widget
 * --------------------------------------------------------------------------
 * @return {this} 
 * --------------------------------------------------------------------------
 */
FDGeneralWidget.prototype.refresh = function(data) {
  this.updateData(data);
  this.draw(this.widgetData);
  return this;
}

/**
 * @function updateData
 * --------------------------------------------------------------------------
 * Updates the widget data
 * @param {dictionary} data | the table data
 * @return {this} 
 * --------------------------------------------------------------------------
 */
FDGeneralWidget.prototype.updateData = function(data) {
  this.widgetData = data;
  return this;
}

/* -------------------------------------------------------------------------- *
 *                                   EVENTS                                   *
 * -------------------------------------------------------------------------- */
