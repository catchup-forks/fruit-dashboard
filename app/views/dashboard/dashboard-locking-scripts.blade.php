<script type="text/javascript">
var gridsterSelector = $('.item.active > .gridster');
// Set initial lock state
setDashboardLock(gridsterSelector.attr("data-dashboard-id"), gridsterSelector.attr("data-lock-direction") == 'lock' ? true : false, false);

/**
 * @listens | element(s): $('#dashboard-lock') | event:click
 * --------------------------------------------------------------------------
 * Triggers the dashboard lock functions on click event
 * --------------------------------------------------------------------------
 */
$('#dashboard-lock').click(function() {
  // Get the dashboard
  dashboardID = $(this).attr("data-dashboard-id");
  direction   = $(this).attr("data-lock-direction") == 'lock' ? true : false;

  // Call  the function
  toggleDashboardLock(dashboardID, direction);

  // Return
  return true;
});

/**
 * @function setDashboardLock
 * --------------------------------------------------------------------------
 * Sets the lock parameters for a given dashboard
 * @param  {number} id | The ID of the dashboard.
 * @param  {boolean} direction | true on lock, false on unlock
 * @param  {boolean} fixTooltips | fix on true
 * @return {null} None
 * --------------------------------------------------------------------------
 */
function setDashboardLock(id, direction, fixTooltips) {
  fixTooltips = typeof fixTooltips !== 'undefined' ? fixTooltips : true;
  var lock = $('#dashboard-lock');
  var options = {
    'class-false' : 'fa fa-unlock-alt fa-2x fa-inverse color-hovered',
    'class-true' : 'fa fa-lock fa-2x fa-inverse color-hovered',
    'tooltip-false' : 'This dashboard is unlocked. Click to lock.',
    'tooltip-true' : 'This dashboard is locked. Click to unlock.',
    'lock-direction-false' : 'lock',
    'lock-direction-true' : 'unlock' 
  };

  // Hide shown tooltip.
  lock.tooltip('hide');

  if (direction) {
    // Set the lock icon
    lock.children('span').attr('class', options['class-true']);
    // Set the tooltip text
    lock.attr('title', options['tooltip-true']);
    // Set the lock direction
    lock.attr('data-lock-direction', options['lock-direction-true']);
    // Set the dashboard lock direction
    gridsterSelector.attr("data-lock-direction", options['lock-direction-true']);

  } else {
    // Set the lock icon
    lock.children('span').attr('class', options['class-false']);
    // Set the tooltip text
    lock.attr('title', options['tooltip-false']);
    // Set the lock direction
    lock.attr('data-lock-direction', options['lock-direction-false']);
    // Set the dashboard lock direction
    gridsterSelector.attr("data-lock-direction", options['lock-direction-false']);
  };

  if (fixTooltips) {
    // Reinitialize tooltip
    lock.tooltip('fixTitle');  
  }
  
  // Set the Dashboard ID
  lock.attr('data-dashboard-id', id);

}



/**
 * @function toggleDashboardLock
 * --------------------------------------------------------------------------
 * Toggles the lock option for the given dashboard
 * @param  {number} id | The ID of the dashboard.
 * @param  {boolean} direction | true on lock, false on unlock
 * @return {null} None
 * --------------------------------------------------------------------------
 */
function toggleDashboardLock(id, direction) {
  // Change lock icon
  setDashboardLock(id, direction);

  // Change gridster
  changeGridster(id, direction);

  // Call ajax
  callLockToggleAjax(id, direction);
}


/**
 * @function changeGridster
 * --------------------------------------------------------------------------
 * Changes the gridster settings based on the new lock option
 * @param  {number} id | The ID of the dashboard.
 * @param  {boolean} direction | true on lock, false on unlock
 * @return {null} None
 * --------------------------------------------------------------------------
 */
function changeGridster(id, direction) {
  // Initialize variables
  var gridster = window['FDGridster' + id];

  if (direction) {
    gridster.lock();
  } else {
    gridster.unlock();
  };
}

/**
 * @function callLockToggleAjax
 * --------------------------------------------------------------------------
 * Calls the locking method to save state in the database. Reverts the whole
 *    process on fail.
 * @param  {number} id | The ID of the dashboard.
 * @param  {boolean} direction | true on lock, false on unlock
 * @return {null} None
 * --------------------------------------------------------------------------
 */
function callLockToggleAjax(id, direction) {
  // Initialize variables based on the direction
  if (direction) {
    var url = "{{ route('dashboard.lock', 'dashboardID') }}".replace('dashboardID', id)
    var successmsg = "You successfully locked the dashboard."
    var errormsg = "Something went wrong, we couldn't lock your dashboard."
  } else {
    var url = "{{ route('dashboard.unlock', 'dashboardID') }}".replace('dashboardID', id)
    var successmsg = "You successfully unlocked the dashboard."
    var errormsg = "Something went wrong, we couldn't unlock your dashboard."
  };

  // Call ajax function
  $.ajax({
    type: "POST",
    dataType: 'json',
    url: url,
        data: null,
        success: function(data) {
          easyGrowl('success', successmsg, 3000);
        },
        error: function() {
          easyGrowl('error', errormsg, 3000);
          // Revert the process
          setDashboardLock(id, !direction);
          changeGridster(id, !direction);
        }
    });
}

</script>