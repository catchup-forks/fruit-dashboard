/**
 * @class FDGreetingsWidget
 * --------------------------------------------------------------------------
 * Class function for the GreetingsWidget
 * --------------------------------------------------------------------------
 */
var FDGreetingsWidget = function(widgetOptions) {
 // Call parent constructor
 FDGeneralWidget.call(this, widgetOptions)
 
 // Plus attributes
 this.containerSelector = '#greeting-' + this.options.general.id;
 this.greetingSelector = '.greeting';

 // Automatically initialize
 this.init();
};

FDGreetingsWidget.prototype = Object.create(FDGeneralWidget.prototype);
FDGreetingsWidget.prototype.constructor = FDGreetingsWidget;

/* -------------------------------------------------------------------------- *
 *                                 FUNCTIONS                                  *
 * -------------------------------------------------------------------------- */

/**
 * @function draw
 * Draws the widget
 * --------------------------------------------------------------------------
 * @return {this} 
 * --------------------------------------------------------------------------
 */
FDGreetingsWidget.prototype.draw = function(data) {
  if (data) {
    $(this.containerSelector).fitText(2.2, {'minFontSize': 24});
    $(this.greetingSelector).html(data['timeOfTheDay']);
  }
  return this;
}
