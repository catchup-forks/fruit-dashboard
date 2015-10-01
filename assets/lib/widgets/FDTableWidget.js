/**
 * @class FDTableWidget
 * --------------------------------------------------------------------------
 * Class function for the Histogram Widgets
 * --------------------------------------------------------------------------
 */
var FDTableWidget = function(widgetOptions) {
  /* -------------------------------------------------------------------------- *
   *                                 ATTRIBUTES                                 *
   * -------------------------------------------------------------------------- */
  this.options = widgetOptions;
  this.table   = new FDTable(this.options);

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
FDTableWidget.prototype.init = function() {
   this.table.updateData(window['widgetData' + this.options.general.id]);
   this.table.draw();
   return this;
};

/**
  * @function init
  * Reinitializes the widget
  * --------------------------------------------------------------------------
  * @return {this} 
  * --------------------------------------------------------------------------
  */
FDTableWidget.prototype.reinit = function() {
   // No need to redraw, table is responsive
   return this;
};

/**
 * @function refresh
 * Handles the specific refresh procedure to the widget
 * --------------------------------------------------------------------------
 * @return {this} 
 * --------------------------------------------------------------------------
 */
FDTableWidget.prototype.refresh = function(data) {
  this.table.updateData(data);
  this.table.draw();
  return this;
}

/* -------------------------------------------------------------------------- *
 *                                   EVENTS                                   *
 * -------------------------------------------------------------------------- */
