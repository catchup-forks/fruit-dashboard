/**
 * @class FDQuoteWidget
 * --------------------------------------------------------------------------
 * Class function for the Quote Widget
 * --------------------------------------------------------------------------
 */
function FDQuoteWidget(widgetOptions) {
  // Private variables
  var options = widgetOptions;
  var quoteSelector  = '#quote-' + widgetOptions.id;
  var authorSelector = '#author-' + widgetOptions.id;
  
  // Public functions
  this.refresh = refresh;

  /**
   * @function refresh
   * Handles the specific refresh procedure to the widget
   * --------------------------------------------------------------------------
   * @return {this} 
   * --------------------------------------------------------------------------
   */
  function refresh(data) {
    $(quoteSelector).html(data['quote']);
    $(authorSelector).html(data['author']);
    return this;
  }

  //   $(document).ready(function() {
  //     @if((Carbon::now()->timestamp - $widget->data->updated_at->timestamp) / 60 > $widget->dataManager()->update_period)
  //       refreshWidget({{ $widget->id }}, function (data) { updateWidget(data);});
  //     @endif
} // FDQuoteWidget
