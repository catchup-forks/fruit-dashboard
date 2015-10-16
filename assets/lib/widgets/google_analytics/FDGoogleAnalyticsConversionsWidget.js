/**
 * @class FDGoogleAnalyticsConversionsWidget
 * --------------------------------------------------------------------------
 * Class function for the GoogleAnalyticsConversions Widget
 * --------------------------------------------------------------------------
 */
function FDGoogleAnalyticsConversionsWidget(widgetOptions) {
  // Call parent constructor
  FDTableWidget.call(this, widgetOptions);
};

FDGoogleAnalyticsConversionsWidget.prototype = Object.create(FDTableWidget.prototype);
FDGoogleAnalyticsConversionsWidget.prototype.constructor = FDGoogleAnalyticsConversionsWidget;
