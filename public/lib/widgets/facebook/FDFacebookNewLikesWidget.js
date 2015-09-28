/**
 * @class FDFacebookNewLikesWidget
 * --------------------------------------------------------------------------
 * Class function for the FacebookNewLikes Widget
 * --------------------------------------------------------------------------
 */
function FDFacebookNewLikesWidget(widgetOptions) {
  // Call parent constructor
  FDHistogramWidget.call(this, widgetOptions);
};

FDFacebookNewLikesWidget.prototype = Object.create(FDHistogramWidget.prototype);
FDFacebookNewLikesWidget.prototype.constructor = FDFacebookNewLikesWidget;

