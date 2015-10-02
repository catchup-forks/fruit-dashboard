/**
 * @class FDGoogleAnalyticsSessionsPerUserWidget
 * --------------------------------------------------------------------------
 * Class function for the GoogleAnalyticsSessionsPerUser Widget
 * --------------------------------------------------------------------------
 */
function FDGoogleAnalyticsSessionsPerUserWidget(widgetOptions) {
  // Call parent constructor
  FDHistogramWidget.call(this, widgetOptions);
};

FDGoogleAnalyticsSessionsPerUserWidget.prototype = Object.create(FDHistogramWidget.prototype);
FDGoogleAnalyticsSessionsPerUserWidget.prototype.constructor = FDGoogleAnalyticsSessionsPerUserWidget;