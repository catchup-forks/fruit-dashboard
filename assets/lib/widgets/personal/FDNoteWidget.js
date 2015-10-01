/**
 * @class FDNoteWidget
 * --------------------------------------------------------------------------
 * Class function for the NoteWidget
 * --------------------------------------------------------------------------
 */
var FDNoteWidget = function(widgetOptions) {
 // Call parent constructor
 FDGeneralWidget.call(this, widgetOptions)
 
 // Plus attributes
 this.widgetData = null;

 // Automatically initialize
 this.init();
};

FDNoteWidget.prototype = Object.create(FDGeneralWidget.prototype);
FDNoteWidget.prototype.constructor = FDNoteWidget;
