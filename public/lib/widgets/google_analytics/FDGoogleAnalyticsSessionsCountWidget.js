/**
 * @class FDGoogleAnalyticsSessionsCountWidget
 * --------------------------------------------------------------------------
 * Class function for the GoogleAnalyticsSessionsCount Widget
 * --------------------------------------------------------------------------
 */
var FDGoogleAnalyticsSessionsCountWidget = function(widgetOptions) {
 // Call parent constructor
 FDGeneralWidget.call(this, widgetOptions)
 
 // Automatically initialize
 this.init();
};

FDGoogleAnalyticsSessionsCountWidget.prototype = Object.create(FDGeneralWidget.prototype);
FDGoogleAnalyticsSessionsCountWidget.prototype.constructor = FDGoogleAnalyticsSessionsCountWidget;

/**
 * @function draw
 * Draws the widget
 * --------------------------------------------------------------------------
 * @return {this} 
 * --------------------------------------------------------------------------
 */
FDGoogleAnalyticsSessionsCountWidget.prototype.draw = function(data) {
  return this;
}