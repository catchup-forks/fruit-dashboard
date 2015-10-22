/**
 * @class FDCalculatorWidget
 * --------------------------------------------------------------------------
 * Class function for the Calculator Widget
 * --------------------------------------------------------------------------
 */
var FDCalculatorWidget = function(widgetOptions) {
 // Call parent constructor
 FDGeneralWidget.call(this, widgetOptions)

 // Automatically initialize
 this.init();
};

FDCalculatorWidget.prototype = Object.create(FDGeneralWidget.prototype);
FDCalculatorWidget.prototype.constructor = FDCalculatorWidget;

/**
 * @function draw
 * Draws the widget
 * --------------------------------------------------------------------------
 * @return {this}
 * --------------------------------------------------------------------------
 */
FDCalculatorWidget.prototype.draw = function(data) {
  return this;
}