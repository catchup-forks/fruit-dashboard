/**
 * @class FDQuoteWidget
 * --------------------------------------------------------------------------
 * Class function for the Quote Widget
 * --------------------------------------------------------------------------
 */
 var FDQuoteWidget = function(widgetOptions) {
 // Call parent constructor
 FDGeneralWidget.call(this, widgetOptions)
 
 // Plus attributes
 this.quoteSelector  = '#quote-' + this.options.general.id;
 this.authorSelector = '#author-' + this.options.general.id;

 // Automatically initialize
 this.init();
};

FDQuoteWidget.prototype = Object.create(FDGeneralWidget.prototype);
FDQuoteWidget.prototype.constructor = FDQuoteWidget;

/**
 * @function draw
 * Draws the widget
 * --------------------------------------------------------------------------
 * @return {this} 
 * --------------------------------------------------------------------------
 */
FDQuoteWidget.prototype.draw = function(data) {
  if (data) {
    $(this.quoteSelector).html(data['quote']);
    $(this.authorSelector).html(data['author']);
  }
  return this;
}

//   $(document).ready(function() {
//     @if((Carbon::now()->timestamp - $widget->data->updated_at->timestamp) / 60 > $widget->dataManager()->update_period)
//       refreshWidget({{ $widget->id }}, function (data) { updateWidget(data);});
//     @endif
