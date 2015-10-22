/**
 * @class FDFacebookLikesWidget
 * --------------------------------------------------------------------------
 * Class function for the FacebookLikes Widget
 * --------------------------------------------------------------------------
 */
function FDFacebookLikesWidget(widgetOptions) {
  // Call parent constructor
  FDHistogramWidget.call(this, widgetOptions);
};

FDFacebookLikesWidget.prototype = Object.create(FDHistogramWidget.prototype);
FDFacebookLikesWidget.prototype.constructor = FDFacebookLikesWidget;
