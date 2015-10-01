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
  this.chart      = new FDChart({
                          selector: this.options.selectors.gridster + this.options.selectors.widget,
                          page: this.options.data.page
                    });

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
   this.chart.draw('line', this.widgetData);
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
   this.chart.draw('line', this.widgetData);
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
  this.chart.draw('line', this.widgetData);
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
  var processedData = {
    labels  : [],
    datasets: [
      {
        values: [],
        name:   this.options.general.name,
        color: '105 ,153, 209'
      }
    ],
  };

  // Transform raw db data (rawData->processedData)
  if (rawData == undefined) {
    processedData = rawData;
  } else if ("datasets" in rawData) {
    processedData = rawData;
  } else {
    for (i = 0; i < rawData.length; ++i) {
      processedData.labels.push(rawData[i]['datetime']);
      processedData.datasets[0].values.push(rawData[i]['value']);
    }
  }

  this.widgetData = processedData;

  // Return
  return this;
}

/* -------------------------------------------------------------------------- *
 *                                   EVENTS                                   *
 * -------------------------------------------------------------------------- */
