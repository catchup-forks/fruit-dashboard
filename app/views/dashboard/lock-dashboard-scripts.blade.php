<script type="text/javascript">

function toggleDashboardLock(id, number) {
    // Get lock icon
    var icon = $('#dashboard-lock-' + id);
    var gridster = $('#gridster-' + number + ' ul');
    console.log(gridster);
    var direction = icon.hasClass('fa-unlock-alt') ? 'locking' : 'unlocking';
    var successmsg = '';
    var errormsg = '';
   
    // Change icons and messages based on the direction
    if (direction == 'locking') {
      icon.toggleClass('fa-unlock-alt').toggleClass('fa-lock')
      successmsg = "You successfully locked this dashboard."
      errormsg = "Something went wrong, we couldn't lock your dashboard."
    } else {
      icon.toggleClass('fa-lock').toggleClass('fa-unlock-alt');
      successmsg = "You successfully unlocked this dashboard."
      errormsg = "Something went wrong, we couldn't unlock your dashboard."
    };

    // Call ajax function
    $.ajax({
      type: "POST",
      dataType: 'json',
      url: "{{ route('dashboard.toggle-lock', 'dashboardID') }}".replace('dashboardID', id),
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

              // Change spinner to icon
              if (direction == 'locking') {
                icon.toggleClass('fa-lock').toggleClass('fa-unlock-alt');
              } else {
                icon.toggleClass('fa-unlock-alt').toggleClass('fa-lock');
              };
          }
    });
}

</script>