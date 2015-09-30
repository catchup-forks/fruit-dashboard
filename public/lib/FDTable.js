/**
 * @class FDTable
 * --------------------------------------------------------------------------
 * Class function for the tables
 * --------------------------------------------------------------------------
 */
function FDTable(widgetOptions) {
  // Private variables
  var options   = widgetOptions;
  var selector = '#widget-wrapper-' + widgetOptions.id + ' table';
  var tableData = null;
  
  // Public functions
  this.draw       = draw;
  this.updateData = updateData;

  /**
   * @function updateData
   * --------------------------------------------------------------------------
   * Updates the table data
   * @param {dictionary} data | the table data
   * @return {this} 
   * --------------------------------------------------------------------------
   */
  function updateData(data) {
    // Transform and store new data
    transformData(data);
    // return
    return this;
  }

  /**
   * @function transformData
   * --------------------------------------------------------------------------
   * Transforms the data to HTML Table format
   * @param {dictionary} rawData | the table data
   * @return {this} stores the transformed data
   * --------------------------------------------------------------------------
   */
  function transformData(rawData) {
    transformedData = {
      header: '',
      content: ''
    }

    // Error handling
    if (rawData == undefined) {} 
    else if (!('header' in rawData)) {} 
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
    tableData = transformedData;

    // Return
    return this;
  }

  /**
   * @function clear
   * --------------------------------------------------------------------------
   * Clears the previous table
   * @return {this}
   * --------------------------------------------------------------------------
   */
  function clear() {
    $(selector + " thead").remove();
    $(selector + " tbody").remove();
  }

  /**
   * @function draw
   * --------------------------------------------------------------------------
   * Draws the chart
   * @param {string} type | the chart type
   * @return {true} 
   * --------------------------------------------------------------------------
   */
  function draw(type) {
    // Clear the existing table
    clear();

    // Draw table
    $(selector).append(tableData.header);
    $(selector).append(tableData.content);

    // return
    return true;
  }

} // FDTable
