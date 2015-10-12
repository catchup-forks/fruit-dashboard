<script type="text/javascript">

  // Define the Hopscotch tour.
  var tour = {
    id: "introduction",
    steps: [
      {
        title: "New widget",
        content: "Add a new widget by clicking the + sign.",
        target: document.querySelector(".fa-plus-circle"),
        placement: "top"
      },
      {
        title: "Hover widget",
        content: "You can move, setup & delete a widget by hovering it.",
        target: document.querySelector(".item.active > .gridster > ul > li"),
        placement: "bottom",
        xOffset: "center",
        arrowOffset: "center"
      },
      {
        title: "Dashboard indicators",
        content: "Clicking these dots take you to one of your dashboards.",
        target: document.querySelector("ol > li"),
        placement: "top",
        xOffset: "center",
        arrowOffset: "center"
      },
      {
        title: "Lock dashboard",
        content: "You can lock or unlock the grid on your dashboard.",
        target: document.querySelector(".item.active > .lock-icon span"),
        placement: "left",
        yOffset: -150,
        arrowOffset: 80
      },
      {
        title: "Navigation",
        content: "You can navigate between your dashboards by clicking the sides of the page.",
        target: document.querySelector(".carousel-control.right"),
        placement: "left",
        yOffset: "center",
        arrowOffset: "center"
      },
      {
        title: "Settings",
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

    // Check if the user has more than one dashboard
    if ($(".carousel-control")[0] == null) {
      // POP the widget step, if there are no widgets on the dashboard
      newTour['steps'].splice(4,1);
    }

    // Check if the dashboard has any widget
    if ($(".item.active > .gridster > ul > li")[0] == null) {
      // POP the widget step, if there are no widgets on the dashboard
      newTour['steps'].splice(1,1);
    }


    // Start tour
    hopscotch.startTour(newTour);
  }

  @if(Input::get('tour'))
    startTour();
  @endif

</script>