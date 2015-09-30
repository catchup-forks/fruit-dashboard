/**
 * @class FDWebhookHistogramWidget
 * --------------------------------------------------------------------------
 * Class function for the Webhook Histogram Widget
 * --------------------------------------------------------------------------
 */
function FDWebhookHistogramWidget(widgetOptions) {
  // Call parent constructor
  FDHistogramWidget.call(this, widgetOptions);
};

FDWebhookHistogramWidget.prototype = Object.create(FDHistogramWidget.prototype);
FDWebhookHistogramWidget.prototype.constructor = FDWebhookHistogramWidget;

