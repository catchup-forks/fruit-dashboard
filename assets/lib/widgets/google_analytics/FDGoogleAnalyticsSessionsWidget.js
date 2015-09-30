/**
 * @class FDGoogleAnalyticsSessionsWidget
 * --------------------------------------------------------------------------
 * Class function for the GoogleAnalyticsSessions Widget
 * --------------------------------------------------------------------------
 */
function FDGoogleAnalyticsSessionsWidget(widgetOptions) {
  // Call parent constructor
  FDHistogramWidget.call(this, widgetOptions);
};

FDGoogleAnalyticsSessionsWidget.prototype = Object.create(FDHistogramWidget.prototype);
FDGoogleAnalyticsSessionsWidget.prototype.constructor = FDGoogleAnalyticsSessionsWidget;

