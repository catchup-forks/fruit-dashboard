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
 this.quoteSelector  = '#quote-' + this.options.id;
 this.authorSelector = '#author-' + this.options.id;
 this.widgetData = null;

 // Automatically initialize
 this.init();
};

FDQuoteWidget.prototype = Object.create(FDGeneralWidget.prototype);
FDQuoteWidget.prototype.constructor = FDQuoteWidget;

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
FDQuoteWidget.prototype.init = function() {
  // Call parent init
  FDGeneralWidget.prototype.init.call(this);
  // Update data
  this.updateData(window['widgetData' + this.options.id]);
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
FDQuoteWidget.prototype.reinit = function() {
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
FDQuoteWidget.prototype.refresh = function(data) {
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
FDQuoteWidget.prototype.updateData = function(data) {
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
FDQuoteWidget.prototype.draw = function() {
  $(this.quoteSelector).html(this.widgetData['quote']);
  $(this.authorSelector).html(this.widgetData['author']);
  return this;
}

//   $(document).ready(function() {
//     @if((Carbon::now()->timestamp - $widget->data->updated_at->timestamp) / 60 > $widget->dataManager()->update_period)
//       refreshWidget({{ $widget->id }}, function (data) { updateWidget(data);});
//     @endif
