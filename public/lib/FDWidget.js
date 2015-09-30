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

  // For debugging
  var logging = false;

  // Public functions
  this.send    = send;
  this.load    = load;
  this.refresh = refresh;
  this.remove  = remove;
  this.getSelector  = getSelector;

  /* -------------------------------------------------------------------------- *
   *                                 FUNCTIONS                                  *
   * -------------------------------------------------------------------------- */

  /**
   * @function getSelector
   * --------------------------------------------------------------------------
   * Returns the widget HTML selector
   * @return {string} selector | The widget selector
   * --------------------------------------------------------------------------
   */
  function getSelector() {
    return selector;
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
    if (logging) { console.log('Sending data for widget #' + options.id); }
    if (logging) { console.log(data); }

    $.ajax({
      type: "POST",
      data: data,
      url: options.postUrl,
    }).done(function(data) {
        if (logging) { console.log('...response arrived | Sending data for widget #' + options.id); }
        callback(data);
        if (logging) { console.log('...callback executed | Sending data for widget #' + options.id); }
    });
    if (logging) { console.log('...done | Sending data for widget #' + options.id); }
  }

  /**
   * @function load
   * --------------------------------------------------------------------------
   * Loads the widget
   * @return {loads the widget}
   * --------------------------------------------------------------------------
   */
  function load() {
    if (logging) { console.log('Loading data for widget #' + options.id); }
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
    if (logging) { console.log('...done | Loading data for widget #' + options.id); }
  };

  /**
   * @function refresh
   * --------------------------------------------------------------------------
   * Refreshes the widget
   * @return {executes the callback function}
   * --------------------------------------------------------------------------
   */
  function refresh() {
    if (logging) { console.log('Refreshing data for widget #' + options.id); }
    // Show loading state
    $(wrapperSelector).hide();
    $(loadingSelector).show();
    // Send refresh data token
    send({'refresh_data': true}, function(){});
    // Poll widget state, and load if finished
    load();
    if (logging) { console.log('...done | Refreshing data for widget #' + options.id); }
  };

  /**
   * @function reinit
   * --------------------------------------------------------------------------
   * Reinitilaizes the widget
   * @return {executes the function}
   * --------------------------------------------------------------------------
   */
  function reinit() {
    if (logging) { console.log('ReInitializing widget #' + options.id); }
    specific.reinit();
    if (logging) { console.log('...done | ReInitializing widget #' + options.id); }
  };

  /**
   * @function remove
   * --------------------------------------------------------------------------
   * Sends the deletion signal to the server
   * @return {null}
   * --------------------------------------------------------------------------
   */
  function remove() {
    if (logging) { console.log('Removing widget #' + options.id); }
    // Call ajax
    $.ajax({
      type: "POST",
      data: null,
      url: options.deleteUrl,
      success: function(data) {
        easyGrowl('success', "You successfully deleted the widget", 3000);
      },
      error: function(){
        easyGrowl('error', "Something went wrong, we couldn't delete your widget. Please try again.", 3000);
      }
    });
    if (logging) { console.log('...done | Removing widget #' + options.id); }
  }

  /* -------------------------------------------------------------------------- *
   *                                   EVENTS                                   *
   * -------------------------------------------------------------------------- */
   
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

  /**
   * @event $(selector).resize
   * --------------------------------------------------------------------------
   * 
   * --------------------------------------------------------------------------
   */
  $(selector).resize(function() {
    reinit();
  });

  /**
   * @event $('.carousel').on('slid.bs.carousel')
   * --------------------------------------------------------------------------
   * Refreshes the widget on carousel slid 
   * --------------------------------------------------------------------------
   */
  $('.carousel').on('slid.bs.carousel', function () {
    reinit();
  })

} // FDWidget

/* -------------------------------------------------------------------------- *
 *                         WIDGET RELATED INITIALIZERS                        *
 * -------------------------------------------------------------------------- */
 // Call the Hamburger Menu.
 $('.dropdown-toggle').dropdown();


 /* -------------------------------------------------------------------------- *
  *                          WIDGET RELATED EVENTS                             *
  * -------------------------------------------------------------------------- */
 // If the mouse leaves the hamburger menu, close it.
 $(".dropdown-menu").mouseleave(function(){
   $(".dropdown").removeClass("open");
 });