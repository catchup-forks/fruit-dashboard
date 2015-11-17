/**
 * @class FDApiHistogramWidget
 * --------------------------------------------------------------------------
 * Class function for the Api Histogram Widget
 * --------------------------------------------------------------------------
 */
function FDApiHistogramWidget(widgetOptions) {
  // Call parent constructor
  FDVisualizer.call(this, widgetOptions);
};

FDApiHistogramWidget.prototype = Object.create(FDVisualizer.prototype);
FDApiHistogramWidget.prototype.constructor = FDApiHistogramWidget;
