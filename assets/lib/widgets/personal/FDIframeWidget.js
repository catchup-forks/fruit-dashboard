/**
 * @class FDIframeWidget
 * --------------------------------------------------------------------------
 * Class function for the IframeWidget
 * --------------------------------------------------------------------------
 */
var FDIframeWidget = function(widgetOptions) {
 // Call parent constructor
 FDGeneralWidget.call(this, widgetOptions)
 
 // Automatically initialize
 this.init();
};

FDIframeWidget.prototype = Object.create(FDGeneralWidget.prototype);
FDIframeWidget.prototype.constructor = FDIframeWidget;

/**
 * @function draw
 * Draws the widget
 * --------------------------------------------------------------------------
 * @return {this} 
 * --------------------------------------------------------------------------
 */
FDIframeWidget.prototype.draw = function(data) {
  return this;
}