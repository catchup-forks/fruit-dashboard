/**
 * @class FDTwitterMentionsWidget
 * --------------------------------------------------------------------------
 * Class function for the TwitterMentions Widget
 * --------------------------------------------------------------------------
 */
function FDTwitterMentionsWidget(widgetOptions) {
  // Private variables
  var options = widgetOptions;
  
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
    // function updateMentionsWidget(data, containerId) {
    //   if (data.length === undefined) {
    //     return;
    //   }
    //   console.log("hello");

    //   function clearContainer() {
    //     $(containerId).html('');
    //   }

    //   for (word in data['text']) {
    //     console.log(word);
    //   }

    //   clearContainer();

    // }
    return this;
  }

} // FDTwitterMentionsWidget
