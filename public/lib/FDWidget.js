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
  var widgetClass     = 'FD' + options.type.replace(/_/g,' ').replace(/\w+/g, function (g) { return g.charAt(0).toUpperCase() + g.substr(1).toLowerCase(); }).replace(/ /g,'') + 'Widget';
  var specific        = new window[widgetClass](options);
  var selector        = '.gridster-player[data-id='+ options.id +']';
  var wrapperSelector = '#widget-wrapper-' + options.id;
  var loadingSelector = '#widget-loading-' + options.id;
  var refreshSelector = '#refresh-' + options.id;

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
   * @param {function} callback | The executable function after the post
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
   * @return {loads the widget}
   * --------------------------------------------------------------------------
   */
  function load() {
    var done = false;

    // Poll the state until the data is ready
    function pollState() {
      send({'state_query': true}, function (data) {
        if (data['ready']) {
          $(loadingSelector).hide();
          $(wrapperSelector).show();
          done = true;
          specific.refresh(data['data']);
        } else if (data['error']) {
          done = true;
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
   * @return {executes the callback function}
   * --------------------------------------------------------------------------
   */
  function refresh() {
    // Show loading state
    $(wrapperSelector).hide();
    $(loadingSelector).show();
    
    // Send refresh data token
    send({'refresh_data': true}, function(){});

    // Poll widget state, and load if finished
    load();
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
   * 
   * --------------------------------------------------------------------------
   */
  $(selector).resize(function() {
    //console.log(size());
  });

  /**
   * @event $(refreshSelector).click
   * --------------------------------------------------------------------------
   * Handles the refresh widget event
   * --------------------------------------------------------------------------
   */
   $(refreshSelector).click(function (e) {
    e.preventDefault();
    refresh();
   });


} // FDWidget

