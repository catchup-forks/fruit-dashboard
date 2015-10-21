/**
 * @class FDTable
 * --------------------------------------------------------------------------
 * Class function for the tables
 * --------------------------------------------------------------------------
 */
function FDTable(widgetSelector) {
  // Private variables
  var selector = widgetSelector + ' table';
  
  // Public functions
  this.draw = draw;

  /**
   * @function draw
   * --------------------------------------------------------------------------
   * Draws the chart
   * @param {dictionary} data | the table data
   * @return {true} 
   * --------------------------------------------------------------------------
   */
  function draw(data) {
    // Clear the existing table
    clear();

    // Draw table
    $(selector).append(data.header);
    $(selector).append(data.content);

    // return
    return true;
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

} // FDTable
