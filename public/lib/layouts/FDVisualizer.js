/**
 * @class FDVisualizer
 * --------------------------------------------------------------------------
 * Class function for the Histogram Widgets
 * --------------------------------------------------------------------------
 */
var FDVisualizer = function(widgetOptions) {
  /* -------------------------------------------------------------------------- *
   *                                 ATTRIBUTES                                 *
   * -------------------------------------------------------------------------- */
  // Initialize debug
  this.debug   = true;
  // Initialize options
  this.options = widgetOptions;
  // Initialize data
  this.data    = {};
  // Initialize possible layouts
  this.possibleLayouts = [
    {name: 'count',             engine: 'count'},
    {name: 'diff',              engine: 'diff'},
    // FIXME: REMOVE CHART AFTER LAYOUT REDESING
    {name: 'chart',             engine: 'chart'},
    {name: 'combined_chart',    engine: 'chart'},
    // -------------------
    {name: 'single-line',       engine: 'chart'},
    {name: 'multi-line',        engine: 'chart'},
    {name: 'combined-bar-line', engine: 'chart'},
    {name: 'table',             engine: 'table'},
  ]
  this.count = new FDCount(this.options);
  //this.diff  = new FDDiff(this.options);
  this.chart = new FDChart(this.options);
  this.table = new FDTable(this.options);
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
FDVisualizer.prototype.init = function() {
  if (this.debug) {console.log('[I] ----- Drawing widget ' + this.options.general.id + ' | ' + this.options.general.type)};
  // Check initial data validity and update
  this.updateData(window[this.options.data.init]);
  // Check initial layout validity and updat
  this.updateLayout(this.options.layout);
  // Call the draw function based on the current layout and data
  this.callDraw(this.options.layout);
  // Return   
  return this;
};

/**
  * @function reinit
  * Reinitializes the widget with the given layout or with the default
  * This function doesn't save the layout, only redraws the widget.
  * --------------------------------------------------------------------------
  * @param [optional] {string} layout | The new layout, one of this.possibleLayouts
  * @return {this}
  * --------------------------------------------------------------------------
  */
FDVisualizer.prototype.reinit = function(layout) {
  if (layout) {
    // Layout supplied
    this.callDraw(layout);
  } else {
    // Layout not supplied, call the default
    this.callDraw(this.options.layout);
  }
  // Return
  return this;
};

/**
 * @function refresh
 * Refreshes the widget with the given layout and data
 * All parameters are optional, but if supplied, they will be saved.
 * --------------------------------------------------------------------------
 * @param [optional] {string} layout | The new layout, one of this.possibleLayouts
 * @param [optional] {dictionary} data | the chart data
 * @return {this}
 * --------------------------------------------------------------------------
 */
FDVisualizer.prototype.refresh = function(layout, data) {
  // Update data if supplied
  if (data) { this.updateData(data); }
  // Update layout if supplied
  if (layout) { this.updateLayout(layout); }
  // Call the draw function based on the current layout and data
  this.callDraw(this.options.layout);
  //Return
  return this;
}

/**
 * @function updateData
 * --------------------------------------------------------------------------
 * Transforms the data to ChartJS format and stores it
 * @param {dictionary} data | the chart data
 * @return {this} stores the transformed data
 * --------------------------------------------------------------------------
 */
FDVisualizer.prototype.updateData = function(data) {
  // Helper to check empty data
  var isEmpty = function(obj) {
    for (var key in obj) return false; return true;
  }

  // Silent fail on epty data
  if (isEmpty(data)) {
    // ---- debug -----------------------------------------------------------
    if (this.debug) {console.log('[E] The supplied data is empty.')};
    // ----------------------------------------------------------------------
  } else {
    this.data = data;
    // ---- debug -----------------------------------------------------------
    if (this.debug) {console.log('[S] Data refreshed to the following:')};
    if (this.debug) {console.log(data)};
    // ----------------------------------------------------------------------
  }
  // Return
  return this;
}

/**
 * @function updateLayout
 * --------------------------------------------------------------------------
 * Transforms the data to ChartJS format and stores it
 * @param {string} layout | The new layout, one ofthis.possibleLayouts
 * @return {this} stores the new layout
 * --------------------------------------------------------------------------
 */
FDVisualizer.prototype.updateLayout = function(layout) {
  // Enable change only to possible layouts
  if ((this.possibleLayouts.filter(function(obj) { return obj.name === layout; })).length) {
    this.options.layout = layout;
    // ---- debug -----------------------------------------------------------
    if (this.debug) {console.log('[S] Layout changed to: ' + layout)};
    // ----------------------------------------------------------------------
  } else {
    // ---- debug -----------------------------------------------------------
    if (this.debug) {console.log('[E] Invalid layout option supplied: ' + layout)};
    // ----------------------------------------------------------------------
  }
  // Return
  return this;
}

/**
  * @function callDraw
  * Calls the draw function based on the given layout
  * --------------------------------------------------------------------------
  * @param {string} layout | The layout, one of this.possibleLayouts
  * @return {this}
  * --------------------------------------------------------------------------
  */
FDVisualizer.prototype.callDraw = function(layout) {
  // Hide all layouts
  $(this.options.selectors.layoutsWrapper).children().each(function() {
    $(this).removeClass('active').hide();
  });

  // Get new layout engine
  layoutArray = this.possibleLayouts.filter(function(obj) { return obj.name === layout; })
  
  // Enable change only to possible layouts
  if (layoutArray.length) {
    // ---- debug -----------------------------------------------------------
    if (this.debug) {console.log('[S] Changing active layout to ' + layout)};
    // ----------------------------------------------------------------------
    // Show new layout
    $(this.options.selectors.layoutsWrapper).find("[id*='"+ layout +"']").first().addClass('active').show();
    // ---- debug -----------------------------------------------------------
    if (this.debug) {console.log('[S] Draw called with layout: ' + layout)};
    // ----------------------------------------------------------------------
    // Call draw for the selected engine
    this[layoutArray[0].engine].draw(layout, this.data[layout])
  } else {
    // ---- debug -----------------------------------------------------------
    if (this.debug) {console.log('[E] Invalid layout option supplied for draw: ' + layout)};
    // ----------------------------------------------------------------------
  }
  // Return
  return this;
};

/* -------------------------------------------------------------------------- *
 *                                   EVENTS                                   *
 * -------------------------------------------------------------------------- */
