/**
 * @class FDGoogleAnalyticsUsersWidget
 * --------------------------------------------------------------------------
 * Class function for the GoogleAnalyticsUsers Widget
 * --------------------------------------------------------------------------
 */
function FDGoogleAnalyticsUsersWidget(widgetOptions) {
  // Call parent constructor
  FDHistogramWidget.call(this, widgetOptions);
};

FDGoogleAnalyticsUsersWidget.prototype = Object.create(FDHistogramWidget.prototype);
FDGoogleAnalyticsUsersWidget.prototype.constructor = FDGoogleAnalyticsUsersWidget;

