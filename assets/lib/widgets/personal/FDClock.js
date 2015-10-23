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
 this.digitalSelector = '#digital-clock-' + this.options.general.id;
 this.analogueSelector = '#analogue-clock-' + this.options.general.id;

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
  this.updateData(window['widgetData' + this.options.general.id]);
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
  this.draw(refresh=false);  
}

/**
 * @function draw
 * Draws the widget
 * --------------------------------------------------------------------------
 * @return {this}
 * --------------------------------------------------------------------------
 */
FDClockWidget.prototype.draw = function(refresh) {
  if (this.widgetData) {
    if (this.widgetData.type == 'digital') {
      this.setTime(refresh);
    }
  };
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

    updateClock();

    if (refresh) {
      // Call again in 2000 ms
      setInterval(updateClock, 2000);
    };
}