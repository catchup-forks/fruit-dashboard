/**
 * @class FDBraintreeArpuWidget
 * --------------------------------------------------------------------------
 * Class function for the BraintreeArpu Widget
 * --------------------------------------------------------------------------
 */
function FDBraintreeArpuWidget(widgetOptions) {
  // Call parent constructor
  FDHistogramWidget.call(this, widgetOptions);
};

FDBraintreeArpuWidget.prototype = Object.create(FDHistogramWidget.prototype);
FDBraintreeArpuWidget.prototype.constructor = FDBraintreeArpuWidget;


