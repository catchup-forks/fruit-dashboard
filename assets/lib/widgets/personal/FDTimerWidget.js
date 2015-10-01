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
 this.digitalSelector = '#digital-clock-' + this.options.general.id;
 this.analogueSelector = '#analogue-clock-' + this.options.general.id;
 this.widgetData = null;

 // Automatically initialize
 this.init();
};

FDTimerWidget.prototype = Object.create(FDGeneralWidget.prototype);
FDTimerWidget.prototype.constructor = FDTimerWidget;
