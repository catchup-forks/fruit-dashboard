/**
 * @class FDClockWidget
 * --------------------------------------------------------------------------
 * Class function for the ClockWidget
 * --------------------------------------------------------------------------
 */
var FDClockWidget = function(widgetOptions) {
 // Call parent constructor
 FDGeneralWidget.call(this, widgetOptions)
 
 // Plus attributes
 this.digitalSelector = '#digital-clock-' + widgetOptions.id;
 this.analogueSelector = '#analogue-clock-' + widgetOptions.id;
 this.widgetData = null;

 // Automatically initialize
 this.init();
};

FDClockWidget.prototype = Object.create(FDGeneralWidget.prototype);
FDClockWidget.prototype.constructor = FDClockWidget;

/* -------------------------------------------------------------------------- *
 *                                 FUNCTIONS                                  *
 * -------------------------------------------------------------------------- */
/**
 * @function init
 * --------------------------------------------------------------------------
 * Override parent init, add clock refresh on every 500ms
 * @return {this} 
 * --------------------------------------------------------------------------
 */
FDClockWidget.prototype.init = function() {
  // Call parent init
  FDGeneralWidget.prototype.init.call(this);
  // Update data
  this.updateData(window['widgetData' + this.options.id]);
  // Draw with refresh call
  this.draw(refresh=true);  
}

/**
 * @function reinit
 * --------------------------------------------------------------------------
 * Override parent reinit
 * @return {this} 
 * --------------------------------------------------------------------------
 */
FDClockWidget.prototype.reinit = function() {
  // Call parent init
  FDGeneralWidget.prototype.reinit.call(this);
  // Draw without refresh call
  this.draw(refresh=false);  
}

/**
 * @function updateData
 * --------------------------------------------------------------------------
 * Updates the table data
 * @param {dictionary} data | the table data
 * @return {this} 
 * --------------------------------------------------------------------------
 */
FDClockWidget.prototype.updateData = function(data) {
  // Store new data
  this.widgetData = data;
  // return
  return this;
}

/**
 * @function draw
 * Draws the widget
 * --------------------------------------------------------------------------
 * @return {this} 
 * --------------------------------------------------------------------------
 */
FDClockWidget.prototype.draw = function(refresh) {
  if (this.widgetData.type == 'digital') {
    this.setTime(refresh);
  }
  return this;
}

/**
 * @function setTime
 * Sets the time for the Clock widget
 * --------------------------------------------------------------------------
 * @return {null} 
 * --------------------------------------------------------------------------
 */
FDClockWidget.prototype.setTime = function(refresh) {
    // Needed because of the scopes
    var that = this
    
    // Formatter function
    function formatTime(time) {
      if (time < 10) {time = "0" + time};
      return time;
    }

    // Update clock
    var updateClock = function() {
      var h = formatTime(new Date().getHours());
      var m = formatTime(new Date().getMinutes());
      $(that.digitalSelector).fitText(0.3, { 'minFontSize': 35 });
      $(that.digitalSelector).html(h + ':' + m);
    }

    if (refresh) {
      // Call again in 2000 ms
      setInterval(updateClock, 2000);
    };
}