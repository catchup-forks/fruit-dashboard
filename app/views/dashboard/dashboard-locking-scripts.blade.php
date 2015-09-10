<script type="text/javascript">

/**
 * @listens | element(s): $('.lock-icon') | event:click
 * --------------------------------------------------------------------------
 * Triggers the dashboard lock functions on click event
 * --------------------------------------------------------------------------
 */
$('.lock-icon').click(function() {
  // Get the dashboard
  dashboardID = $(this).attr("data-dashboard-id");
  direction   = $(this).attr("data-lock-direction") == 'lock' ? true : false;

  // Call  the function
  toggleDashboardLock(dashboardID, direction);

  // Return
  return true;
});

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
  changeLockIcon(id, direction);

  // Change gridster
  changeGridster(id, direction);

  // Call ajax
  callLockToggleAjax(id, direction);
}


/**
 * @function changeLockIcon
 * --------------------------------------------------------------------------
 * Changes the lock icon for a dsahboard based on the direction
 * @param  {number} id | The ID of the dashboard.
 * @param  {boolean} direction | true on lock, false on unlock
 * @return {null} None
 * --------------------------------------------------------------------------
 */
function changeLockIcon(id, direction) {
  // Initialize variables
  selector = $(".lock-icon[data-dashboard-id='" + id + "']");

  // Change the icon
  if (direction) {
    selector.attr('data-lock-direction', 'unlock');
    //oldselector.tooltip("option", "content", 'This dashboard is locked. Click to unlock.');
    selector.find('span').removeClass('fa-unlock-alt').addClass('fa-lock');
  } else {
    selector.attr('data-lock-direction', 'lock');
    //oldselector.tooltip("option", "content", 'This dashboard is unlocked. Click to lock.');
    selector.find('span').removeClass('fa-lock').addClass('fa-unlock-alt');
  };
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
  var gridster = window['Gridster' + id];

  if (direction) { 
    gridster.lockGrid();
  } else {
    gridster.unlockGrid();
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
          $.growl.notice({
            title: "Success!",
            message: successmsg,
            size: "large",
            duration: 3000,
            location: "br"
          });
        },
        error: function() {
          $.growl.error({
            title: "Error!",
            message: errormsg,
            size: "large",
            duration: 3000,
            location: "br"
          });

          // Revert the process
          changeLockIcon(id, !direction);
          changeGridster(id, !direction);
        }
    });
}

</script>