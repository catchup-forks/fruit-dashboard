/**
 * @class FDFacebookPageImpressionsWidget
 * --------------------------------------------------------------------------
 * Class function for the FacebookPageImpressions Widget
 * --------------------------------------------------------------------------
 */
function FDFacebookPageImpressionsWidget(widgetOptions) {
  // Call parent constructor
  FDHistogramWidget.call(this, widgetOptions);
};

FDFacebookPageImpressionsWidget.prototype = Object.create(FDHistogramWidget.prototype);
FDFacebookPageImpressionsWidget.prototype.constructor = FDFacebookPageImpressionsWidget;
