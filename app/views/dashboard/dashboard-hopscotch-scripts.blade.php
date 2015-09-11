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
        placement: "left"
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
    // Deep copy tour variable (always reinitialize)
    var newTour = {};
    $.extend(true, newTour, tour);

    // Check if the dashboard has any widget
    if ($(".item.active > .gridster > ul > li")[0] == null) {
      // POP the widget step, if there are no widgets on the dashboard
      newTour['steps'].splice(1,1);
    }

    // Start tour
    hopscotch.startTour(newTour);
  }

  @if(Request::input('tour'))
    startTour();
  @endif

</script>