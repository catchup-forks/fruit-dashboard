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

