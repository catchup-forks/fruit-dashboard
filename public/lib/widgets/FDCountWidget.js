
/**
 * @class FDCountWidget
 * --------------------------------------------------------------------------
 * Class function for the Count Widgets
 * --------------------------------------------------------------------------
 */
var FDCountWidget = function(widgetOptions) {
  /* -------------------------------------------------------------------------- *
   *                                 ATTRIBUTES                                 *
   * -------------------------------------------------------------------------- */
  this.options    = widgetOptions;
  this.widgetData = null;

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
FDCountWidget.prototype.init = function() {
  return this;
};

/**
  * @function init
  * Reinitializes the widget
  * --------------------------------------------------------------------------
  * @return {this}
  * --------------------------------------------------------------------------
  */
FDCountWidget.prototype.reinit = function() {
   // No need to redraw, table is responsive
   return this;
};

/**
 * @function refresh
 * Handles the specific refresh procedure to the widget
 * --------------------------------------------------------------------------
 * @param {dictionary} data | the new table data
 * @return {this}
 * --------------------------------------------------------------------------
 */
FDCountWidget.prototype.refresh = function(data) {
  $('#' + this.options.selectors.count).tooltip({'html': true});
  return this;
}
