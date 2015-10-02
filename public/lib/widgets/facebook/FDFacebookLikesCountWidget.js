/**
 * @class FDFacebookLikesCountWidget
 * --------------------------------------------------------------------------
 * Class function for the FacebookLikesCount Widget
 * --------------------------------------------------------------------------
 */
var FDFacebookLikesCountWidget = function(widgetOptions) {
 // Call parent constructor
 FDGeneralWidget.call(this, widgetOptions)
 
 // Automatically initialize
 this.init();
};

FDFacebookLikesCountWidget.prototype = Object.create(FDGeneralWidget.prototype);
FDFacebookLikesCountWidget.prototype.constructor = FDFacebookLikesCountWidget;
