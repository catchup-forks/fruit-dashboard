<script type="text/javascript">

/**
 * @listens | element(s): $('.make-default-icon') | event:click
 * --------------------------------------------------------------------------
 * Triggers the make default functions on click event
 * --------------------------------------------------------------------------
 */
$('.make-default-icon').click(function() {
  // Get the old default dashboard ID
  oldDashboardID = $('.make-default-icon.default').attr("data-dashboard-id");

  // Get the new default dashboard ID
  newDashboardID = $(this).attr("data-dashboard-id");

  // Call  the function
  makeDefault(oldDashboardID, newDashboardID);

  // Return
  return true;
});

/**
 * @function makeDefault
 * --------------------------------------------------------------------------
 * Makes a dashboard the default one
 * @param  {number} id | The ID of the dashboard.
 * @return {null} None
 * --------------------------------------------------------------------------
 */
function makeDefault(oldID, newID) {
  // Change lock icon
  changeDefaultIcon(oldID, newID);

  // Call ajax
  callMakeDefaultAjax(oldID, newID);
}

/**
 * @function changeDefaultIcon
 * --------------------------------------------------------------------------
 * Changes the make default icons on gthe page
 * @param  {number} oldID | The ID of the old default dashboard.
 * @param  {number} newID | The ID of the new default dashboard.
 * @return {null} None
 * --------------------------------------------------------------------------
 */
function changeDefaultIcon(oldID, newID) {
  // Initialize variables
  oldselector = $(".make-default-icon[data-dashboard-id='" + oldID + "']");
  newselector = $(".make-default-icon[data-dashboard-id='" + newID + "']");

  // Change the old icon
  oldselector.removeClass('default');
  oldselector.tooltip('hide');
  oldselector.attr('data-original-title', 'Make this the default dashboard.');
  oldselector.tooltip('fixTitle');
  oldselector.find('span').removeClass('label-danger').addClass('label-primary');

  // Change the new icon
  newselector.addClass('default');
  newselector.tooltip('hide');
  newselector.attr('data-original-title', 'This is your default dashboard.');
  newselector.tooltip('fixTitle');
  newselector.find('span').removeClass('label-primary').addClass('label-danger');
}

/**
 * @function callMakeDefaultAjax
 * --------------------------------------------------------------------------
 * Calls the make default method to save state in the database. Reverts the whole
 *    process on fail.
 * @param  {number} id | The ID of the dashboard.
 * @return {null} None
 * --------------------------------------------------------------------------
 */
function callMakeDefaultAjax(oldID, newID) {
  // Initialize variables based on the direction
  var url = "{{ route('dashboard.makedefault', 'dashboardID') }}".replace('dashboardID', newID)
  var successmsg = "You successfully changed your default dashboard."
  var errormsg = "Something went wrong, we couldn't change your default."

  // Call ajax function
  $.ajax({
    type: "POST",
    dataType: 'json',
    url: url,
    data: null,
    success: function(data) {
      if(data) {
        easyGrowl('success', successmsg, 3000);
      } else {
        easyGrowl('error', errormsg, 3000);
        // Revert the process
        changeDefaultIcon(newID, oldID);
      }
    },
    error: function() {
      easyGrowl('error', errormsg, 3000);
      // Revert the process
      changeDefaultIcon(newID, oldID);
    }
  });
}
</script>