/**
 * @class FDStripeArpuWidget
 * --------------------------------------------------------------------------
 * Class function for the StripeArpu Widget
 * --------------------------------------------------------------------------
 */
function FDStripeArpuWidget(widgetOptions) {
  // Call parent constructor
  FDVisualizer.call(this, widgetOptions);
};

FDStripeArpuWidget.prototype = Object.create(FDVisualizer.prototype);
FDStripeArpuWidget.prototype.constructor = FDStripeArpuWidget;
