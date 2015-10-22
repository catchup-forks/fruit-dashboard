/**
 * @class FDGoogleAnalyticsActiveUsersWidget
 * --------------------------------------------------------------------------
 * Class function for the GoogleAnalyticsActiveUsers Widget
 * --------------------------------------------------------------------------
 */
function FDGoogleAnalyticsActiveUsersWidget(widgetOptions) {
  // Call parent constructor
  FDHistogramWidget.call(this, widgetOptions);
};

FDGoogleAnalyticsActiveUsersWidget.prototype = Object.create(FDHistogramWidget.prototype);
FDGoogleAnalyticsActiveUsersWidget.prototype.constructor = FDGoogleAnalyticsActiveUsersWidget;

