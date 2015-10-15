/**
 * @class FDGoogleAnalyticsSessionsCountWidget
 * --------------------------------------------------------------------------
 * Class function for the GoogleAnalyticsSessionsCount Widget
 * --------------------------------------------------------------------------
 */
var FDGoogleAnalyticsSessionsCountWidget = function(widgetOptions) {
 // Call parent constructor
 FDCountWidget.call(this, widgetOptions)

 // Automatically initialize
 this.init();
};

FDGoogleAnalyticsSessionsCountWidget.prototype = Object.create(FDCountWidget.prototype);
FDGoogleAnalyticsSessionsCountWidget.prototype.constructor = FDGoogleAnalyticsSessionsCountWidget;
