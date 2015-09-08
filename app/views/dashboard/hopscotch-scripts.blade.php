<script type="text/javascript">

  // Define the Hopscotch tour.
  var tour = {
    id: "introduction",
      steps: [
        {
          title: "new widget",
          content: "Add a new widget by clicking the + sign.",
          target: document.querySelector(".fa-plus-circle"),
          placement: "top"
        },
        {
          title: "hover widget",
          content: "You can move, setup & delete a widget by hovering it.",
          target: document.querySelector(".item.active > .gridster > ul > li"),
          placement: "bottom",
          xOffset: "center",
          arrowOffset: "center"
        },
        {
          title: "dashboard indicators",
          content: "Clicking these dots take you to one of your dashboards.",
          target: document.querySelector("ol > li"),
          placement: "top",
          xOffset: "center",
          arrowOffset: "center"
        },
        {
          title: "lock dashboard",
          content: "You can lock or unlock the grid on your dashboard.",
          target: document.querySelector(".item.active > .lock-icon span"),
          placement: "top"
        },               
        {
          title: "settings",
          content: "More stuff here.",
          target: document.querySelector(".fa-cog"),
          placement: "left"
        }
      ]
  };

  function startTour() {
    // Check if the dashboard has any widget
    if ($(".item.active > .gridster > ul > li")) {
      // Start the Hopscotch tour.
      hopscotch.startTour(tour);
    
    } else {
      // POP the widget step, because there are no widgets on the dashboard
      hopscotch.startTour(tour.splice(1,1));
    }
  }

  @if(Request::input('tour'))
    startTour();
  @endif

</script>