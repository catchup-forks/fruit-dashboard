/**
 * @class FDPromoWidget
 * --------------------------------------------------------------------------
 * Class function for the Promo Widget
 * --------------------------------------------------------------------------
 */
var FDPromoWidget = function(widgetOptions) {
 // Call parent constructor
 FDGeneralWidget.call(this, widgetOptions)

 var isDragging = false;

 this.options       = widgetOptions;
 this.promoSelector = '#promo-' + this.options.general.id;

 // Automatically initialize
 this.init();

 function setMouseDownEvent() {
    $(this.promoSelector).mousedown(function() {
      isDragging = false;
    });
  }

  function setMouseMoveEvent() {
    $(this.promoSelector).mousemove(function() {
      isDragging = true;
    });
  }

  function setMouseUpEvent() {
    if($(this.promoSelector).length>0) {
      var ev = $._data($(this.promoSelector)[0], 'events');
      if(ev && ev.mouseup===undefined) {
        $(this.promoSelector).mouseup(function() {
          var wasDragging = isDragging;
          if (!wasDragging) {
            //window.location = options.urls.statUrl;
          }
          isDragging = false;
        });
      }
    }
  }
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

   // Add drag events
   if (options.features.drag) {
      setMouseDownEvent();
      setMouseMoveEvent();
      setMouseUpEvent();
    }

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

