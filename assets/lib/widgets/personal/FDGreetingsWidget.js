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
 this.widgetData = null;

 // Automatically initialize
 this.init();
};

FDGreetingsWidget.prototype = Object.create(FDGeneralWidget.prototype);
FDGreetingsWidget.prototype.constructor = FDGreetingsWidget;

/* -------------------------------------------------------------------------- *
 *                                 FUNCTIONS                                  *
 * -------------------------------------------------------------------------- */
/**
 * @function init
 * --------------------------------------------------------------------------
 * Override parent init, add clock refresh on every 500ms
 * @return {this} 
 * --------------------------------------------------------------------------
 */
FDGreetingsWidget.prototype.init = function() {
  // Call parent init
  FDGeneralWidget.prototype.init.call(this);
  // Update data
  this.updateData(window['widgetData' + this.options.general.id]);
  // Draw with refresh call
  this.draw();  
}

/**
 * @function reinit
 * --------------------------------------------------------------------------
 * Override parent reinit
 * @return {this} 
 * --------------------------------------------------------------------------
 */
FDGreetingsWidget.prototype.reinit = function() {
  // Call parent init
  FDGeneralWidget.prototype.reinit.call(this);
  // Draw
  this.draw();  
}

/**
 * @function refresh
 * --------------------------------------------------------------------------
 * Override parent refresh
 * @return {this} 
 * --------------------------------------------------------------------------
 */
FDGreetingsWidget.prototype.refresh = function(data) {
  // Call parent init
  FDGeneralWidget.prototype.refresh.call(this, data);
  // Update data
  this.updateData(data);
  // Draw
  this.draw();  
}

/**
 * @function updateData
 * --------------------------------------------------------------------------
 * Updates the table data
 * @param {dictionary} data | the table data
 * @return {this} 
 * --------------------------------------------------------------------------
 */
FDGreetingsWidget.prototype.updateData = function(data) {
  // Store new data
  this.widgetData = data;
  // return
  return this;
}

/**
 * @function draw
 * Draws the widget
 * --------------------------------------------------------------------------
 * @return {this} 
 * --------------------------------------------------------------------------
 */
FDGreetingsWidget.prototype.draw = function() {
  $(this.containerSelector).fitText(2.2, {'minFontSize': 24});
  $(this.greetingSelector).html(this.widgetData['timeOfTheDay']);
  return this;
}
