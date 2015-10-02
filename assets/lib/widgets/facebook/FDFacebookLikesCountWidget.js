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

/**
 * @function draw
 * Draws the widget
 * --------------------------------------------------------------------------
 * @return {this} 
 * --------------------------------------------------------------------------
 */
FDFacebookLikesCountWidget.prototype.draw = function(data) {
  return this;
}