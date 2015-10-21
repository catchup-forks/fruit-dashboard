/**
 * @class FDStripeEventsWidget
 * --------------------------------------------------------------------------
 * Class function for the StripeEvents Widget
 * --------------------------------------------------------------------------
 */
var FDStripeEventsWidget = function(widgetOptions) {
  // Call parent constructor
  FDGeneralWidget.call(this, widgetOptions)

  // Automatically initialize
  this.init();
};

FDStripeEventsWidget.prototype = Object.create(FDGeneralWidget.prototype);
FDStripeEventsWidget.prototype.constructor = FDStripeEventsWidget;

/**
 * @function draw
 * Draws the widget
 * --------------------------------------------------------------------------
 * @return {this} 
 * --------------------------------------------------------------------------
 */
FDStripeEventsWidget.prototype.draw = function(data) {
  return this;
}