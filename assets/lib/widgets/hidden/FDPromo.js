/**
 * @class FDPromoWidget
 * --------------------------------------------------------------------------
 * Class function for the Promo Widget
 * --------------------------------------------------------------------------
 */
var FDPromoWidget = function(widgetOptions) {
 // Call parent constructor
 FDGeneralWidget.call(this, widgetOptions)

 this.options       = widgetOptions;
 this.promoSelector = '#promo-' + this.options.general.id;

  // Automatically initialize
  this.init();
};

FDPromoWidget.prototype = Object.create(FDGeneralWidget.prototype);
FDPromoWidget.prototype.constructor = FDPromoWidget;

/* -------------------------------------------------------------------------- *
 *                                 FUNCTIONS                                  *
 * -------------------------------------------------------------------------- */

/**
  * @function init
  * Automatically initializes the widget
  * --------------------------------------------------------------------------
  * @return {this} 
  * --------------------------------------------------------------------------
  */
FDPromoWidget.prototype.init = function() {
   this.updateData(window[this.options.data.init]);
   this.draw(this.widgetData);

   return this;
};

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
