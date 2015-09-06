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
          target: document.querySelector(".fa-plus-circle"),
          placement: "top"
        },
        {
          title: "dashboard indicators",
          content: "Clicking these dots take you to one of your dashboards.",
          target: document.querySelector("ol > li"),
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

  // Start the Hopscotch tour.
  // hopscotch.startTour(tour);

</script>