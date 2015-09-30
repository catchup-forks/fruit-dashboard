/**
 * @class FDGoogleAnalyticsTopSourcesWidget
 * --------------------------------------------------------------------------
 * Class function for the GoogleAnalyticsTopSources Widget
 * --------------------------------------------------------------------------
 */
function FDGoogleAnalyticsTopSourcesWidget(widgetOptions) {
  // Call parent constructor
  FDTableWidget.call(this, widgetOptions);
};

FDGoogleAnalyticsTopSourcesWidget.prototype = Object.create(FDTableWidget.prototype);
FDGoogleAnalyticsTopSourcesWidget.prototype.constructor = FDGoogleAnalyticsTopSourcesWidget;
