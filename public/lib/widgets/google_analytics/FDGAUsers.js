/**
 * @class FDGoogleAnalyticsUsersChartWidget
 * --------------------------------------------------------------------------
 * Class function for the GoogleAnalyticsUsersChart Widget
 * --------------------------------------------------------------------------
 */
function FDGoogleAnalyticsUsersChartWidget(widgetOptions) {
  // Call parent constructor
  FDHistogramWidget.call(this, widgetOptions);
};

FDGoogleAnalyticsUsersChartWidget.prototype = Object.create(FDHistogramWidget.prototype);
FDGoogleAnalyticsUsersChartWidget.prototype.constructor = FDGoogleAnalyticsUsersChartWidget;

