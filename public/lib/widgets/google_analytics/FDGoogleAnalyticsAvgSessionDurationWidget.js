/**
 * @class FDGoogleAnalyticsAvgSessionDurationWidget
 * --------------------------------------------------------------------------
 * Class function for the GoogleAnalyticsAvgSessionDuration Widget
 * --------------------------------------------------------------------------
 */
function FDGoogleAnalyticsAvgSessionDurationWidget(widgetOptions) {
  // Call parent constructor
  FDHistogramWidget.call(this, widgetOptions);
};

FDGoogleAnalyticsAvgSessionDurationWidget.prototype = Object.create(FDHistogramWidget.prototype);
FDGoogleAnalyticsAvgSessionDurationWidget.prototype.constructor = FDGoogleAnalyticsAvgSessionDurationWidget;
