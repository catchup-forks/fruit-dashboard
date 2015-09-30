/**
 * @class FDTextWidget
 * --------------------------------------------------------------------------
 * Class function for the Text Widget
 * --------------------------------------------------------------------------
 */
var FDTextWidget = function(widgetOptions) {
 // Call parent constructor
 FDGeneralWidget.call(this, widgetOptions)
 
 // Plus attributes
 this.digitalSelector = '#digital-clock-' + widgetOptions.id;
 this.analogueSelector = '#analogue-clock-' + widgetOptions.id;
 this.widgetData = null;

 // Automatically initialize
 this.init();
};

FDTextWidget.prototype = Object.create(FDGeneralWidget.prototype);
FDTextWidget.prototype.constructor = FDTextWidget;
