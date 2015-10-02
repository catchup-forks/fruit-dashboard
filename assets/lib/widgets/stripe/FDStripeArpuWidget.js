/**
 * @class FDStripeArpuWidget
 * --------------------------------------------------------------------------
 * Class function for the StripeArpu Widget
 * --------------------------------------------------------------------------
 */
function FDStripeArpuWidget(widgetOptions) {
  // Call parent constructor
  FDHistogramWidget.call(this, widgetOptions);
};

FDStripeArpuWidget.prototype = Object.create(FDHistogramWidget.prototype);
FDStripeArpuWidget.prototype.constructor = FDStripeArpuWidget;
