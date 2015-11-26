/**
 * @class FDPromoWidget
 * --------------------------------------------------------------------------
 * Class function for the Promo Widget
 * --------------------------------------------------------------------------
 */
var FDPromoWidget = function(widgetOptions) {
 // Call parent constructor
 FDGeneralWidget.call(this, widgetOptions)

 this.options        = widgetOptions;
 this.promoSelector  = '#promo-' + this.options.general.id;

  // Automatically initialize
  this.init();

  /* -------------------------------------------------------------------------- *
     *                                  EVENTS                                    *
     * -------------------------------------------------------------------------- */

     /**
      * @event promolink.click
      * --------------------------------------------------------------------------
      * Prevents the default redirect on click
      * --------------------------------------------------------------------------
      */
       $(this.promoSelector).find('a').click(function(e) {
         e.preventDefault();
       })

    /**
     * @event $(this.promoSelector).mousedown
     * --------------------------------------------------------------------------
     * Checks the click/drag moves
     * --------------------------------------------------------------------------
     */
      $(this.promoSelector).mousedown(function() {
        isDragging = false;
      })

    /**
     * @event $(this.promoSelector).mousemove
     * --------------------------------------------------------------------------
     * Checks the click/drag moves
     * --------------------------------------------------------------------------
     */
      $(this.promoSelector).mousemove(function() {
        isDragging = true;
      })

    /**
     * @event $(this.promoSelector).mouseup
     * --------------------------------------------------------------------------
     * Checks the click/drag moves
     * Redirects to appropriate page
     * Opens new page in blank window if iframe
     * --------------------------------------------------------------------------
     */
      $(this.promoSelector).mouseup(function() {
        var wasDragging = isDragging;
        if (!wasDragging) {
          if (window!=window.top) {
            window.open($(this).find('a').attr('href'), '_blank');
          } else {
            window.location = $(this).find('a').attr('href');
          }
        }
        isDragging = false;
      })

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