/**
 * @class FDWidget
 * --------------------------------------------------------------------------
 * Class function for the widgets
 * --------------------------------------------------------------------------
 */
function FDWidget(widgetOptions) {
  /* -------------------------------------------------------------------------- *
   *                                 ATTRIBUTES                                 *
   * -------------------------------------------------------------------------- */
  var options         = widgetOptions;
  var specific        = new window['FD' + options.type.replace(/_/g,' ').replace(/\w+/g, function (g) { return g.charAt(0).toUpperCase() + g.substr(1).toLowerCase(); }).replace(/ /g,'') + 'Widget'];
  var selector        = '.gridster-player[data-id='+ options.id +']';
  var wrapperSelector = '#widget-loading-' + options.id;
  var loadingSelector = '#widget-wrapper-' + options.id;

  // Public functions
  this.size    = size;
  this.send    = send;
  this.load    = load;
  this.refresh = refresh;

  /* -------------------------------------------------------------------------- *
   *                                 FUNCTIONS                                  *
   * -------------------------------------------------------------------------- */

  /**
   * @function size
   * --------------------------------------------------------------------------
   * Returns the widget actual size in pixels
   * @return {dictionary} size | The widget size in pixels
   * --------------------------------------------------------------------------
   */
  function size() {
    return {'width': $(selector).width(),
            'height': $(selector).height()};
  }

  /**
   * @function send
   * --------------------------------------------------------------------------
   * Sends an ajax request to save the widget data
   * @param {json} data | the POST data
   * @param {function} callback | the callback function
   * @return {executes the callback function}
   * --------------------------------------------------------------------------
   */
  function send(data, callback) {
    $.ajax({
      type: "POST",
      data: data,
      url: options.postUrl,
    }).done(function(data) {
        callback(data);
    });
  }

  /**
   * @function load
   * --------------------------------------------------------------------------
   * Loads the widget
   * @param {function} callback | the callback function
   * @return {executes the callback function}
   * --------------------------------------------------------------------------
   */
  function load(widgetId, callback) {
    var done = false;
    
    // Poll the state until the data is ready
    function pollState() {
      send({'state_query': true}, function (data) {
        if (data['ready']) {
          $(loadingSelector).hide();
          $(wrapperSelector).show();
          done = true;
          callback(data['data']);
        }
        if (!done) {
          setTimeout(pollState, 1000);
        }
      });
    }
    pollState();
  };

  /**
   * @function refresh
   * --------------------------------------------------------------------------
   * Refreshes the widget
   * @param {function} callback | the callback function
   * @return {executes the callback function}
   * --------------------------------------------------------------------------
   */
  function refresh(callback) {
    $(wrapperSelector).hide();
    $(loadingSelector).show();
    send({'refresh_data': true}, callback);
    load(callback);
  };

  /* -------------------------------------------------------------------------- *
   *                                   EVENTS                                   *
   * -------------------------------------------------------------------------- */

  /**
   * @event $(document).ready
   * --------------------------------------------------------------------------
   * 
   * --------------------------------------------------------------------------
   */
  $(document).ready(function() {
    //console.log(size());
  });

  /**
   * @event $(selector).resize
   * --------------------------------------------------------------------------
   * Class function for the widgets
   * --------------------------------------------------------------------------
   */
  $(selector).resize(function() {
    //console.log(size());
  });


} // FDWidget

