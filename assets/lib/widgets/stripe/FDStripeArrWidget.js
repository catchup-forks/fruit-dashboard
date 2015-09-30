/**
 * @class FDStripeArrWidget
 * --------------------------------------------------------------------------
 * Class function for the StripeArr Widget
 * --------------------------------------------------------------------------
 */
function FDStripeArrWidget(widgetOptions) {
  // Call parent constructor
  FDHistogramWidget.call(this, widgetOptions);
};

FDStripeArrWidget.prototype = Object.create(FDHistogramWidget.prototype);
FDStripeArrWidget.prototype.constructor = FDStripeArrWidget;

