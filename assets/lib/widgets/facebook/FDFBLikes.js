/**
 * @class FDFacebookLikesWidget
 * --------------------------------------------------------------------------
 * Class function for the FacebookLikes Widget
 * --------------------------------------------------------------------------
 */
function FDFacebookLikesWidget(widgetOptions) {
  // Call parent constructor
  FDVisualizer.call(this, widgetOptions);
};

FDFacebookLikesWidget.prototype = Object.create(FDVisualizer.prototype);
FDFacebookLikesWidget.prototype.constructor = FDFacebookLikesWidget;
