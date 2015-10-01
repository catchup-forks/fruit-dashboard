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
  this.options    = widgetOptions;
  this.widgetData = null;
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
FDTableWidget.prototype.init = function() {
   this.updateData(window[this.options.data.init]);
   this.table.draw(this.widgetData);
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
 * @param {dictionary} data | the new table data
 * @return {this} 
 * --------------------------------------------------------------------------
 */
FDTableWidget.prototype.refresh = function(data) {
  this.updateData(data);
  this.table.draw(this.widgetData);
  return this;
}

/** 
 * @function updateData
 * --------------------------------------------------------------------------
 * Transforms the data to HTML Table format and stores it
 * @param {dictionary} rawData | the table data
 * @return {this} stores the transformed data
 * --------------------------------------------------------------------------
 */
FDTableWidget.prototype.updateData = function(rawData) {
  var transformedData = { header: '', content: '' };

  // Error handling
  if (rawData == undefined) {} 
  else if (!('header' in rawData)) {} 
  // Transforming data
  else {
    // Adding header
    transformedData.header = '<thead>';
    for (var i = 0; i < rawData.header.length; i++) {
      transformedData.header += '<th>' + rawData.header[i] + '</th>';
    };
    transformedData.header += '</thead>';

    // Adding content
    transformedData.content = '<tbody>';
    for (var row=0; row < rawData.content.length; row++) {
      transformedData.content += '<tr>';
      for (var key in rawData.content[row]) {
        transformedData.content += '<td>' + rawData.content[row][key] + '</td>';
      }
      transformedData.content += '</tr>';
    }
      transformedData.content += '</tbody>';
  }

  // Store new data
  this.widgetData = transformedData;

  // Return
  return this;
}
/* -------------------------------------------------------------------------- *
 *                                   EVENTS                                   *
 * -------------------------------------------------------------------------- */
