/**
 * @class FDTextWidget
 * --------------------------------------------------------------------------
 * Class function for the Text Widget
 * --------------------------------------------------------------------------
 */
var FDTextWidget = function(widgetOptions) {
 // Call parent constructor
 FDGeneralWidget.call(this, widgetOptions)
 
 // Automatically initialize
 this.init();
};

FDTextWidget.prototype = Object.create(FDGeneralWidget.prototype);
FDTextWidget.prototype.constructor = FDTextWidget;

/**
 * @function draw
 * Draws the widget
 * --------------------------------------------------------------------------
 * @return {this} 
 * --------------------------------------------------------------------------
 */
FDTextWidget.prototype.draw = function(data) {
  return this;
}