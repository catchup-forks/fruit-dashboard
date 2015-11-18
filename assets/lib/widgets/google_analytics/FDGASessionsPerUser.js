/**
 * @class FDGoogleAnalyticsSessionsPerUserWidget
 * --------------------------------------------------------------------------
 * Class function for the GoogleAnalyticsSessionsPerUser Widget
 * --------------------------------------------------------------------------
 */
function FDGoogleAnalyticsSessionsPerUserWidget(widgetOptions) {
  // Call parent constructor
  FDVisualizer.call(this, widgetOptions);
};

FDGoogleAnalyticsSessionsPerUserWidget.prototype = Object.create(FDVisualizer.prototype);
FDGoogleAnalyticsSessionsPerUserWidget.prototype.constructor = FDGoogleAnalyticsSessionsPerUserWidget;