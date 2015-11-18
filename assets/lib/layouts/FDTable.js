/**
 * @class FDTable
 * --------------------------------------------------------------------------
 * Class function for the tables
 * --------------------------------------------------------------------------
 */
function FDTable(widgetOptions) {
  // Private variables
  var options = widgetOptions;
  var tableSelector = options.selectors.activeLayout + ' table'
  
  // Debug
  var debug = true;

  // Public functions
  this.draw = draw;

  /**
   * @function draw
   * --------------------------------------------------------------------------
   * Draws the table
   * @param {string} layout | the table layout
   * @param {dictionary} data | the table data
   * @return {this}
   * --------------------------------------------------------------------------
   */
  function draw(layout, data) {
    // Automatically change the layout parameters to the supplied
    options.layout = layout;
    options.selectors.activeLayout = '#widget-layout-' + layout + '-' + options.general.id;
    tableSelector = options.selectors.activeLayout + ' table';

    // Validate data
    if (!validateData(data)) { 
      return this; 
    }

    // Clear the existing table
    clear();
    
    // Draw table and validate
    try {
      $(tableSelector).append(data.header);
      $(tableSelector).append(data.content);
      if (debug) {console.log('[S] Drawing the table succeeded.')};
    } catch(err) {
      if (debug) {console.log('[E] Drawing the table failed: ')};
      if (debug) {console.log(err.message)};
    }

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
    // Remove table content
    $(tableSelector + " thead").remove();
    $(tableSelector + " tbody").remove();
  }

  /**
   * @function validateData
   * --------------------------------------------------------------------------
   * Validates the table data
   * @param {dictionary} data | the table data
   * @return {boolean} isValid | validity of the data
   * --------------------------------------------------------------------------
   */
  function validateData(data) {
    // Validate data
    isValid = (data && data.header && data.content);
    if (!isValid) {
      if (debug) {console.log('[E] The supplied data is not valid for the table.')};
      if (debug) {console.log(data)};
    } else {
      if (debug) {console.log('[S] Data validation passed.')};
    }
    // Validate data
    return isValid;
  }

} // FDTable
