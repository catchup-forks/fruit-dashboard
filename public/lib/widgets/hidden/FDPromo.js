/**
 * @class FDPromoWidget
 * --------------------------------------------------------------------------
 * Class function for the Promo Widget
 * --------------------------------------------------------------------------
 */
var FDPromoWidget = function(widgetOptions) {
 // Call parent constructor
 FDGeneralWidget.call(this, widgetOptions)

 // Automatically initialize
 this.init();
};

FDPromoWidget.prototype = Object.create(FDGeneralWidget.prototype);
FDPromoWidget.prototype.constructor = FDPromoWidget;

/**
 * @function draw
 * Draws the widget
 * --------------------------------------------------------------------------
 * @return {this}
 * --------------------------------------------------------------------------
 */
FDPromoWidget.prototype.draw = function(data) {
  return this;
}

