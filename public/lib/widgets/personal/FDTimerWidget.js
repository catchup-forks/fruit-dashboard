/**
 * @class FDTimerWidget
 * --------------------------------------------------------------------------
 * Class function for the Timer Widget
 * --------------------------------------------------------------------------
 */
var FDTimerWidget = function(widgetOptions) {
 // Call parent constructor
 FDGeneralWidget.call(this, widgetOptions)
 
 // Plus attributes
 this.digitalSelector = '#digital-clock-' + widgetOptions.id;
 this.analogueSelector = '#analogue-clock-' + widgetOptions.id;
 this.widgetData = null;

 // Automatically initialize
 this.init();
};

FDTimerWidget.prototype = Object.create(FDGeneralWidget.prototype);
FDTimerWidget.prototype.constructor = FDTimerWidget;
