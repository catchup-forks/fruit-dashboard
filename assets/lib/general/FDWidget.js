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
  var widgetSelector  = options.selectors.widget;
  var widgetClass     = 'FD' + options.general.type.replace(/_/g,' ').replace(/\w+/g, function (g) { return g.charAt(0).toUpperCase() + g.substr(1).toLowerCase(); }).replace(/ /g,'') + 'Widget';
  var specific        = new window[widgetClass](options);
  
  var delayTime       = 1000;
  var delayTimer      = function(){};

  // Debug
  var debug = true;

  // Public functions
  this.send    = send;
  this.load    = load;
  this.refresh = refresh;
  this.reinit  = reinit;
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
    return widgetSelector;
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
    if (debug) { console.log('Sending data for widget #' + options.general.id); }
    if (debug) { console.log(data); }

    $.ajax({
      type: "POST",
      data: data,
      url: options.urls.postUrl,
    }).done(function(data) {
        if (debug) { console.log('...response arrived | Sending data for widget #' + options.general.id); }
        if (debug) { console.log(data); }
        callback(data);
        if (debug) { console.log('...callback executed | Sending data for widget #' + options.general.id); }
    });
    if (debug) { console.log('...done | Sending data for widget #' + options.general.id); }
  }

  /**
   * @function load
   * --------------------------------------------------------------------------
   * Loads the widget
   * @return {loads the widget}
   * --------------------------------------------------------------------------
   */
  function load() {
    if (debug) { console.log('Loading data for widget #' + options.general.id); }
    var done = false;

    // Poll the state until the data is ready
    function pollState() {
      send({'state_query': true}, function (data) {
        if (data['ready']) {
          $(options.selectors.wrapper).html(data['html']);
          $(options.selectors.loading).hide();
          $(options.selectors.wrapper).show();
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
    if (debug) { console.log('...done | Loading data for widget #' + options.general.id); }
  };

  /**
   * @function refresh
   * --------------------------------------------------------------------------
   * Refreshes the widget
   * @return {executes the callback function}
   * --------------------------------------------------------------------------
   */
  function refresh() {
    if (debug) { console.log('Refreshing data for widget #' + options.general.id); }
    // Show loading state
    $(options.selectors.wrapper).hide();
    $(options.selectors.loading).show();
    // Send refresh data token
    send({'refresh_data': true}, function(){});
    // Poll widget state, and load if finished
    load();
    if (debug) { console.log('...done | Refreshing data for widget #' + options.general.id); }
  };

  /**
   * @function reinit
   * --------------------------------------------------------------------------
   * Reinitilaizes the widget
   * @return {executes the function}
   * --------------------------------------------------------------------------
   */
  function reinit() {
    if (debug) { console.log('ReInitializing widget #' + options.general.id); }
    specific.reinit();
    if (debug) { console.log('...done | ReInitializing widget #' + options.general.id); }
  };

  /**
   * @function remove
   * --------------------------------------------------------------------------
   * Sends the deletion signal to the server
   * @return {null}
   * --------------------------------------------------------------------------
   */
  function remove() {
    if (debug) { console.log('Removing widget #' + options.general.id); }
    // Call ajax
    $.ajax({
      type: "POST",
      data: null,
      url: options.urls.deleteUrl,
      success: function(data) {
        easyGrowl('success', "You successfully deleted the widget", 3000);
      },
      error: function(){
        easyGrowl('error', "Something went wrong, we couldn't delete your widget. Please try again.", 3000);
      }
    });
    if (debug) { console.log('...done | Removing widget #' + options.general.id); }
  }

  /**
   * @function changeLayout
   * --------------------------------------------------------------------------
   * Redraws the widget content.
   * @param {string} layout | the name of the layout
   * @return {changes the layout}
   * --------------------------------------------------------------------------
   */
  function changeLayout(layout, save) {
    var save = save || false;
    if (save) { 
      specific.reinit(layout);
      if (debug) {console.log('[S] Layout changed to ' + layout + ' (frontend)');}
    } else {
      specific.refresh(layout);
      if (debug) {console.log('[S] Layout changed to ' + layout + ' (frontend)');}
    }
  };

  /**
   * @function callAjaxLayoutChange
   * --------------------------------------------------------------------------
   * Calls an ajax function to save the selected layout
   * @param {string} layout | the name of the layout
   * @return {sends the ajax}
   * --------------------------------------------------------------------------
   */
  function callAjaxLayoutChange(layout) {
    // Call ajax function
    $.ajax({
      type: "POST",
      dataType: 'json',
      url: options.urls.layoutUrl,
          data: {layout: layout},
          success: function(data) {
            if (debug) {console.log('[S] Layout changed to' + layout + ' (backend)');}
          },
          error: function(data) {
            if (debug) {console.log('[E] Internal error in layout save ajax');}
          }
      });
  }

  /* -------------------------------------------------------------------------- *
   *                                   EVENTS                                   *
   * -------------------------------------------------------------------------- */

  /**
   * @event $(options.selectors.refresh).click
   * --------------------------------------------------------------------------
   * Handles the refresh widget event
   * --------------------------------------------------------------------------
   */
  $(options.selectors.refresh).click(function (e) {
    e.preventDefault();
    refresh();
  });

  /**
   * @event $(selector).resize
   * --------------------------------------------------------------------------
   * Hadles the resize event
   * --------------------------------------------------------------------------
   */
  $(widgetSelector).resize(function() {
    reinit();
  });

  /**
   * @event $(options.selectors.wrapper).hover
   * --------------------------------------------------------------------------
   * Shows / hides the non chart numbers on hover
   * --------------------------------------------------------------------------
   */
  $(options.selectors.wrapper).hover(function(e){
    var widget = $(e.currentTarget);
    widget.find('.chart-value').css('visibility', 'hidden');
    widget.find('.chart-diff-data').css('visibility', 'hidden');
    widget.find('.chart-name').css('visibility', 'hidden');
  }, function(e){
    var widget = $(e.currentTarget);
    widget.find('.chart-value').css('visibility', 'visible');
    widget.find('.chart-diff-data').css('visibility', 'visible');
    widget.find('.chart-name').css('visibility', 'visible');
  });

  /**
   * @event $(options.selectors.layoutSelector*).click
   * --------------------------------------------------------------------------
   * Sets the new default layout for the widget.
   * --------------------------------------------------------------------------
   */
  $(options.selectors.layoutSelector + "> .element").click(function() {
    if (debug) { console.log("[I] Clicked layout change on widget #" + options.general.id + " | " + $(this).data('layout'));}
    // Call ajax set to default here
    callAjaxLayoutChange($(this).data('layout'))
    // Remove the active class, if any.
    $(options.selectors.layoutSelector + "> div.active").removeClass('active');
    // Add the active class for the clicked element.
    $(this).addClass("active");
    // Change the current layout
    changeLayout($(this).data('layout'));
  });
} // FDWidget

/* -------------------------------------------------------------------------- *
 *                         WIDGET RELATED INITIALIZERS                        *
 * -------------------------------------------------------------------------- */
 // Call the Hamburger Menu.
 $('.dropdown-toggle').dropdown();


 /* -------------------------------------------------------------------------- *
  *                          WIDGET RELATED EVENTS                             *
  * -------------------------------------------------------------------------- */
 // If the mouse leaves the widget, close dropdown menu.
 $('.gridster-widget').mouseleave(function(){
   $(".dropdown").removeClass("open");
 });

 // If the mouse leaves the dropdown menu, close it.
 $(".dropdown-menu").mouseleave(function(){
   $(".dropdown").removeClass("open");
 });
