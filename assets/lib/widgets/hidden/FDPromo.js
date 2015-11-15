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
   if (this.options.features.drag) {
      this.setMouseDownEvent();
      this.setMouseMoveEvent();
      this.setMouseUpEvent();
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

FDPromoWidget.prototype.setMouseDownEvent = function() {
  $(this.promoSelector).mousedown(function() {
    isDragging = false;
  });
}

FDPromoWidget.prototype.setMouseMoveEvent = function() {
  $(this.promoSelector).mousemove(function() {
    isDragging = true;
  });
}

FDPromoWidget.prototype.setMouseUpEvent = function() {
  var url = this.widgetData.url;
  if($(this.promoSelector).length>0) {
    var ev = $._data($(this.promoSelector)[0], 'events');
    if(ev && ev.mouseup===undefined) {
      $(this.promoSelector).mouseup(function() {
        var wasDragging = isDragging;
        if (!wasDragging) {
          if (window!=window.top) {
            window.open(url, '_blank');
          } else {
            window.location = url;
          }
        }
        isDragging = false;
      });
    }
  }
}

