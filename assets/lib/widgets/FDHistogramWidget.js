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
  this.options    = widgetOptions;
  this.widgetData = null;
  this.chart      = new FDChart(this.options);
  this.table      = new FDTable(this.options.selectors.wrapper);

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
   this.updateData(window[this.options.data.init]);
   if(this.widgetData.type=='chart') {
    this.chart.draw('line', this.widgetData);
   } else if(this.widgetData.type=='table') {
    this.table.draw(this.widgetData);
   }
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
   if(this.widgetData.type=='chart') {
    this.chart.draw('line', this.widgetData);
   } else if(this.widgetData.type=='table') {
    this.table.draw(this.widgetData);
   }
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
  this.updateData(data);
  if(this.widgetData.type=='chart') {
    this.chart.draw('line', this.widgetData);
  } else if(this.widgetData.type=='table') {
    this.table.draw(this.widgetData);
  }
  return this;
}

/**
 * @function updateData
 * --------------------------------------------------------------------------
 * Transforms the data to ChartJS format and stores it
 * @param {dictionary} rawData | the chart data
 * @return {this} stores the transformed data
 * --------------------------------------------------------------------------
 */
FDHistogramWidget.prototype.updateData = function(rawData) {
  this.widgetData = rawData;
  return this;
}

/* -------------------------------------------------------------------------- *
 *                                   EVENTS                                   *
 * -------------------------------------------------------------------------- */
