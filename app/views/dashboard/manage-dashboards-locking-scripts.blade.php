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

  // Call ajax
  callLockToggleAjax(id, direction);
}


/**
 * @function changeLockIcon
 * --------------------------------------------------------------------------
 * Changes the lock icon for a dashboard based on the direction
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
    selector.attr('title', 'This dashboard is locked. Click to unlock.');
    selector.find('span').removeClass('label-primary').addClass('label-danger');
    selector.find('i').removeClass('fa-unlock-alt').addClass('fa-lock');
  } else {
    selector.attr('data-lock-direction', 'lock');
    selector.attr('title', 'This dashboard is unlocked. Click to lock.');
    selector.find('span').removeClass('label-danger').addClass('label-primary');
    selector.find('i').removeClass('fa-lock').addClass('fa-unlock-alt');
  };

  // Reinitialize tooltip
  selector.tooltip('fixTitle').tooltip('show');
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
          changeLockIcon(id, !direction);
        }
    });
}

</script>