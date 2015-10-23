/**
 * @class FDApiHistogramWidget
 * --------------------------------------------------------------------------
 * Class function for the Api Histogram Widget
 * --------------------------------------------------------------------------
 */
function FDApiHistogramWidget(widgetOptions) {
  // Call parent constructor
  FDHistogramWidget.call(this, widgetOptions);
};

FDApiHistogramWidget.prototype = Object.create(FDHistogramWidget.prototype);
FDApiHistogramWidget.prototype.constructor = FDApiHistogramWidget;
