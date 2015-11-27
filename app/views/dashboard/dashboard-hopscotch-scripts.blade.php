<script type="text/javascript">

  // Define the Hopscotch tour.
  var tour = {
    id: "introduction",
    steps: [
      {
        title: "Browse data",
        content: "Browse your dashboards and widgets by using the menu.",
        target: document.querySelector(".menu"),
        placement: "right"
      },
      {
        title: "Hover widget",
        content: "You can move, setup & delete a widget by hovering it.",
        target: document.querySelector(".gridster-widget"),
        placement: "bottom",
        xOffset: "center",
        arrowOffset: "center"
      },
      {
        title: "Select velocity",
        content: "You can set your velocity here.",
        target: document.querySelector(".granularity-menu"),
        placement: "top",
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

    // Check if the dashboard has any widget
    if ($(".gridster-widget")[0] == null) {
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