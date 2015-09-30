/**
 * @class FDIframeWidget
 * --------------------------------------------------------------------------
 * Class function for the IframeWidget
 * --------------------------------------------------------------------------
 */
var FDIframeWidget = function(widgetOptions) {
 // Call parent constructor
 FDGeneralWidget.call(this, widgetOptions)
 
 // Plus attributes
 this.widgetData = null;

 // Automatically initialize
 this.init();
};

FDIframeWidget.prototype = Object.create(FDGeneralWidget.prototype);
FDIframeWidget.prototype.constructor = FDIframeWidget;
