/**
 * @class FDTimerWidget
 * --------------------------------------------------------------------------
 * Class function for the Timer Widget
 * --------------------------------------------------------------------------
 */
var FDTimerWidget = function(widgetOptions) {
 // Call parent constructor
 FDGeneralWidget.call(this, widgetOptions)

 // Automatically initialize
 this.init();
};

FDTimerWidget.prototype = Object.create(FDGeneralWidget.prototype);
FDTimerWidget.prototype.constructor = FDTimerWidget;

/**
 * @function draw
 * Draws the widget
 * --------------------------------------------------------------------------
 * @return {this} 
 * --------------------------------------------------------------------------
 */
FDTimerWidget.prototype.draw = function(data) {
  return this;
}