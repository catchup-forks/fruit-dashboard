/**
 * @class FDGoogleAnalyticsBounceRateWidget
 * --------------------------------------------------------------------------
 * Class function for the GoogleAnalyticsBounceRate Widget
 * --------------------------------------------------------------------------
 */
function FDGoogleAnalyticsBounceRateWidget(widgetOptions) {
  // Call parent constructor
  FDHistogramWidget.call(this, widgetOptions);
};

FDGoogleAnalyticsBounceRateWidget.prototype = Object.create(FDHistogramWidget.prototype);
FDGoogleAnalyticsBounceRateWidget.prototype.constructor = FDGoogleAnalyticsBounceRateWidget;

