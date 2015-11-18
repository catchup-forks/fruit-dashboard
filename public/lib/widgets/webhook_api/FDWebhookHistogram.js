/**
 * @class FDWebhookHistogramWidget
 * --------------------------------------------------------------------------
 * Class function for the Webhook Histogram Widget
 * --------------------------------------------------------------------------
 */
function FDWebhookHistogramWidget(widgetOptions) {
  // Call parent constructor
  FDVisualizer.call(this, widgetOptions);
};

FDWebhookHistogramWidget.prototype = Object.create(FDVisualizer.prototype);
FDWebhookHistogramWidget.prototype.constructor = FDWebhookHistogramWidget;

