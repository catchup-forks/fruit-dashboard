@extends('meta.base-user')

  @section('pageTitle')
    Dashboard
  @stop

  @section('pageStylesheet')
  @stop

  @section('pageContent')

  <div class="container grid-base fill-height">
    <div class="gridster not-visible">
      <ul>
        @foreach ($dashboards as $dashboard)
          @foreach ($dashboard->widgets as $widget)
            @if ($widget->status != 'hidden')
              @include('dashboard.widget', ['widget' => $widget])
            @endif
          @endforeach
        @endforeach
      </ul>
    </div>
  </div> <!-- /.container -->

  @stop

  @section('pageScripts')

  <script type="text/javascript">
    // Gridster builds the interactive dashboard.
    $(function(){

      var gridster;
      var players = $('.gridster li');
      var positioning = [];
      var containerWidth = $('.grid-base').width();
      var containerHeight = $('.grid-base').height();
      var numberOfCols = 12;
      var numberOfRows = 9;
      var margin = 5;
      var widget_width = (containerWidth / numberOfCols) - (margin * 2);
      var widget_height = (containerHeight / numberOfRows) - (margin * 2);

     gridster = $('.gridster ul').gridster({
       widget_base_dimensions: [widget_width, widget_height],
       widget_margins: [margin, margin],
       min_cols: numberOfCols,
       min_rows: numberOfRows,
       snap_up: false,
       serialize_params: function ($w, wgd) {
         return {
           id: $w.data().id,
           col: wgd.col,
           row: wgd.row,
           size_x: wgd.size_x,
           size_y: wgd.size_y,
         };
       },
       resize: {
         enabled: true,
         start: function() {
          players.toggleClass('hovered');
         },
         stop: function(e, ui, $widget) {
          positioning = gridster.serialize();
          positioning = JSON.stringify(positioning);
          $.ajax({
            type: "POST",
            data: {'positioning': positioning},
            url: "/widget/save-position/{{Auth::user()->id}}/"
           });
          players.toggleClass('hovered');
         }
       },
       draggable: {
        start: function() {
          players.toggleClass('hovered');
        },
        stop: function(e, ui, $widget) {
          positioning = gridster.serialize();
          positioning = JSON.stringify(positioning);
          $.ajax({
            type: "POST",
            data: {'positioning': positioning},
            url: "/widget/save-position/{{Auth::user()->id}}/"
          });
          players.toggleClass('hovered');
         }
       }
     }).data('gridster');

      $('.gridster.not-visible').fadeIn(500);

    });
  </script>

  @append

