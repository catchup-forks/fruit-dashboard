/**
 * @class FDCount
 * --------------------------------------------------------------------------
 * Class function for the counts
 * --------------------------------------------------------------------------
 */
function FDCount(widgetOptions) {
  // Private variables
  var options = widgetOptions;
  var countSelector = options.selectors.activeLayout + ' [id*=count]'
  
  // Debug
  var debug = true;

  // Public functions
  this.draw = draw;

  /**
   * @function draw
   * --------------------------------------------------------------------------
   * Draws the count
   * @param {string} layout | the count layout
   * @param {dictionary} data | the count data
   * @return {this}
   * --------------------------------------------------------------------------
   */
  function draw(layout, data) {
    // Automatically change the layout parameters to the supplied
    options.layout = layout;
    options.selectors.activeLayout = '#widget-layout-' + layout + '-' + options.general.id;
    countSelector = options.selectors.activeLayout + ' [id*=count]';

    // Validate data
    if (!validateData(data)) { 
      return this; 
    }

    // Clear the existing count
    clear();
    
    // Draw count and validate
    try {
      // FIXME
      if (debug) {console.log('[S] Drawing the count succeeded.')};
    } catch(err) {
      if (debug) {console.log('[E] Drawing the count failed: ')};
      if (debug) {console.log(err.message)};
    }

    // Return
    return this;
  }

  /**
   * @function clear
   * --------------------------------------------------------------------------
   * Clears the previous count
   * @return {this}
   * --------------------------------------------------------------------------
   */
  function clear() {
    // Remove count content
    // FIXME
  }

  /**
   * @function validateData
   * --------------------------------------------------------------------------
   * Validates the count data
   * @param {dictionary} data | the count data
   * @return {boolean} isValid | validity of the data
   * --------------------------------------------------------------------------
   */
  function validateData(data) {
    // Validate data
    // FIXME
    isValid = true; 
    if (!isValid) {
      if (debug) {console.log('[E] The supplied data is not valid for the count.')};
      if (debug) {console.log(data)};
    } else {
      if (debug) {console.log('[S] Data validation passed.')};
    }
    // Validate data
    return isValid;
  }

} // FDCount
