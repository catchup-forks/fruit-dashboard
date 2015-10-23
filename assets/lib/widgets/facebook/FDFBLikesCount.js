/**
 * @class FDFacebookLikesCountWidget
 * --------------------------------------------------------------------------
 * Class function for the FacebookLikesCount Widget
 * --------------------------------------------------------------------------
 */
var FDFacebookLikesCountWidget = function(widgetOptions) {
 // Call parent constructor
 FDCountWidget.call(this, widgetOptions)

 // Automatically initialize
 this.init();
};

FDFacebookLikesCountWidget.prototype = Object.create(FDCountWidget.prototype);
FDFacebookLikesCountWidget.prototype.constructor = FDFacebookLikesCountWidget;
