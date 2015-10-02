/**
 * @class FDNoteWidget
 * --------------------------------------------------------------------------
 * Class function for the NoteWidget
 * --------------------------------------------------------------------------
 */
var FDNoteWidget = function(widgetOptions) {
 // Call parent constructor
 FDGeneralWidget.call(this, widgetOptions)

 // Automatically initialize
 this.init();
};

FDNoteWidget.prototype = Object.create(FDGeneralWidget.prototype);
FDNoteWidget.prototype.constructor = FDNoteWidget;

/**
 * @function draw
 * Draws the widget
 * --------------------------------------------------------------------------
 * @return {this} 
 * --------------------------------------------------------------------------
 */
FDNoteWidget.prototype.draw = function(data) {
  return this;
}