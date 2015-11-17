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
    {name: 'count',             engine: 'this.count'},
    {name: 'diff',              engine: 'this.diff'},
    {name: 'single-line',       engine: 'this.chart'},
    {name: 'multi-line',        engine: 'this.chart'},
    {name: 'combined-bar-line', engine: 'this.chart'},
    {name: 'table',             engine: 'this.table'},
  ]
  //this.count = new FDCount(this.options);
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
  // Check initial data validity and update
  this.updateData(window[this.options.data.init]);
  // Check initial layout validity and updat
  this.updateLayout(this.options.layout);
  // Call the draw function based on the current layout and data
  this.callDraw(this.options.layout);

   // if(this.options.layout=='chart' || this.options.layout == 'multiple') {
   //   if (this.data.isCombined) {
   //    this.chart.draw('combined', this.data);
   //   } else {
   //    this.chart.draw('line', this.data); 
   //   }
   //  } else if(this.options.layout=='table') {
   //    this.table.draw(this.data, true);
   //  } 
   
   return this;
};

/**
  * @function reinit
  * Reinitializes the widget with the given layout or with the default
  * This function doesn't save the layout, only redraws the widget.
  * --------------------------------------------------------------------------
  * @param [optional] {string} layout | The new layout, one of the followings:
  *    count / diff / single-line / multi-line / combined-bar-line / table
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

  // if(this.options.layout=='chart' || this.options.layout == 'multiple') {
  //   if (this.data.isCombined) {
  //     this.chart.draw('combined', this.data);
  //   } else {
  //     this.chart.draw('line', this.data); 
  //   }
  // } else if(this.options.layout=='table') {
  //   this.table.draw(this.data, true);
  // }
  
  // Return
  return this;
};

/**
 * @function refresh
 * Refreshes the widget with the given layout and data
 * All parameters are optional, but if supplied, they will be saved.
 * --------------------------------------------------------------------------
 * @param [optional] {string} layout | The new layout, one of the followings:
 *    count / diff / single-line / multi-line / combined-bar-line / table
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
  
  // if(this.options.layout=='chart' || this.options.layout == 'multiple') {
  //   if (this.data.isCombined) {
  //     this.chart.draw('combined', this.data);
  //   } else {
  //     this.chart.draw('line', this.data); 
  //   }
  // } else if(this.options.layout=='table') {
  //   this.table.draw(this.data, true);
  // }

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
  console.log(data)
  if (data.length) {
    // ---- debug -----------------------------------------------------------
    this.data = data;
    if (this.debug) {console.log('[S] Data refreshed.' + this.options.data.init)};
    if (this.debug) {console.log(data)};
    // ----------------------------------------------------------------------
  } else {
    // ---- debug -----------------------------------------------------------
    if (this.debug) {console.log('[E] The supplied data is empty.')};
    // ----------------------------------------------------------------------
  }
  return this;
}

/**
 * @function updateLayout
 * --------------------------------------------------------------------------
 * Transforms the data to ChartJS format and stores it
 * @param {string} layout | The new layout, one of the followings
 *   count / diff / single-line / multi-line / combined-bar-line / table
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
  * @param {string} layout | The layout, one of the followings
  *   count / diff / single-line / multi-line / combined-bar-line / table
  * @return {this}
  * --------------------------------------------------------------------------
  */
FDVisualizer.prototype.callDraw = function(layout) {
  // Get layout engine
  layoutArray = this.possibleLayouts.filter(function(obj) { return obj.name === layout; })
  
  // Enable change only to possible layouts
  if (layoutArray.length) {
    // Call draw for the selected engine
    this[layoutArray.engine].draw(this.data)
    // ---- debug -----------------------------------------------------------
    if (this.debug) {console.log('[S] Draw called with layout: ' + layout)};
    // ----------------------------------------------------------------------
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
