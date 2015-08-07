@extends('meta.base-user')

  @section('pageTitle')
    Dashboard
  @stop

  @section('pageStylesheet')
  @stop

  @section('pageContent')

  <div id="dashboards" class="carousel slide">
      
      {{-- Include navigation dots for each dashboard. --}}
      <ol class="carousel-indicators">

        @foreach ($dashboards as $dashboard)

          <li data-target="#dashboards" data-slide-to="{{ $dashboard->number }}" data-toggle="tooltip" data-placement="top" title="{{ $dashboard->name }}" class="drop-shadow @if($dashboard->number == 0) active @endif"></li>

        @endforeach

      </ol>

      {{-- Make a wrapper for dashboards --}}
      <div class="carousel-inner">

        @foreach ($dashboards as $dashboard)

          <div class="item @if($dashboard->number == 0) active @endif">
            
            <div class="fill" style="background-image:url({{ Background::dailyBackgroundURL() }});">
            </div> <!-- /.fill -->

            {{-- Here comes the dashboard content --}}
            <div id="gridster-{{ $dashboard->number }}" class="gridster container grid-base fill-height">

              {{-- Generate all the widgdets --}}
              <ul>

                @foreach ($dashboard->widgets as $widget)
                  
                  @if ($widget->state != 'hidden')
                    @include('widget.widget-general-layout', ['widget' => $widget->getSpecific()])
                  @endif

                @endforeach 
                
              </ul>

            </div> <!-- /.gridster -->

          </div> <!-- /.item -->

        @endforeach

      </div> <!-- /.carousel-inner -->


      {{-- Set the navigational controls on sides. --}}
      <a class="left carousel-control" href="#dashboards" data-slide="prev">
          <span class="icon-prev"></span>
      </a>
      <a class="right carousel-control" href="#dashboards" data-slide="next">
          <span class="icon-next"></span>
      </a>

  </div> <!-- /#dashboards -->



  @stop

  @section('pageScripts')

  {{-- Initialize the tooltip for the Navigational Dots --}}
  <script type="text/javascript">
      $(function () {
        $('[data-toggle="tooltip"]').tooltip({
          html: true
        })
      })
  </script>

  <script>
  $('.carousel').carousel({
      interval: false //stops the auto-cycle
  })
  </script>


  @include('widget.widget-general-scripts')

  <script type="text/javascript">

    // Gridster builds the interactive dashboard.
    @foreach ($dashboards as $dashboard)

      $(function(){

        var gridster{{ $dashboard->number }};
        var players = $('#gridster-{{ $dashboard->number }} li');
        var positioning = [];
        var containerWidth = $('.grid-base').width();
        var containerHeight = $('.grid-base').height();
        var numberOfCols = {{ SiteConstants::getGridNumberOfCols() }};
        var numberOfRows = {{ SiteConstants::getGridNumberOfRows() }};
        var margin = 5;
        var widget_width = (containerWidth / numberOfCols) - (margin * 2);
        var widget_height = (containerHeight / numberOfRows) - (margin * 2);

       gridster{{ $dashboard->number }} = $('#gridster-{{ $dashboard->number }} ul').gridster({
         namespace: '#gridster-{{ $dashboard->number }}',
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
            positioning = gridster{{ $dashboard->number }}.serialize();
            positioning = JSON.stringify(positioning);
            $.ajax({
              type: "POST",
              data: {'positioning': positioning},
              url: "{{ route('widget.save-position', Auth::user()->id) }}"
             });
            players.toggleClass('hovered');
           }
         },
         draggable: {
          start: function() {
            players.toggleClass('hovered');
          },
          stop: function(e, ui, $widget) {
            positioning = gridster{{ $dashboard->number }}.serialize();
            positioning = JSON.stringify(positioning);
            $.ajax({
              type: "POST",
              data: {'positioning': positioning},
              url: "{{ route('widget.save-position', Auth::user()->id) }}"
            });
            players.toggleClass('hovered');
           }
         }
       }).data('gridster');

    });

    @endforeach

    $('.gridster.not-visible').fadeIn(500);

    function loadWidget(widgetId, callback) {
      var done = false;

      function sendAjax() {
        $.ajax({
          type: "POST",
          data: {'state_query': true},
          url: "{{ route('widget.ajax-handler', 'widgetID') }}".replace("widgetID", widgetId)
        }).done(function( data ) {
          if (data['state'] == 'active') {
            $("#widget-loading-" + widgetId).hide();
            $("#widget-wrapper-" + widgetId).show();
            done = true;
            callback(data);
          }
        });
        if (!done) {
          setTimeout(sendAjax, 3000)
        }
      }

      sendAjax();
    };
    // Overriding chartjs defaults.
    Chart.defaults.global.animationSteps = 50;
    Chart.defaults.global.tooltipYPadding = 16;
    Chart.defaults.global.tooltipCornerRadius = 0;
    Chart.defaults.global.tooltipTitleFontStyle = "normal";
    Chart.defaults.global.tooltipFillColor = "rgba(0,160,0,0.8)";
    Chart.defaults.global.animationEasing = "easeOutBounce";
    Chart.defaults.global.responsive = true;
    Chart.defaults.global.scaleLineColor = "black";
    Chart.defaults.global.scaleFontSize = 9;

    var chartOptions = {
       responsive: true,
       pointHitDetectionRadius : 2,
       pointDotRadius : 3,
       bezierCurve: false,
       scaleShowVerticalLines: false,
       tooltipTemplate: "<%if (label){%><%=label %>: <%}%>$<%= value %>",
       animation: false
    };

    function drawLineGraph(canvas, values, labels, name) {
      // Building data.
      var chartData = {
      labels: labels,
      datasets: [{
        label: "{{ $widget->descriptor->name }}",
        fillColor : "rgba(100, 222, 100,0.2)",
        strokeColor : "rgba(100, 222, 100,1)",
        pointColor : "rgba(100, 222, 100,1)",
        pointStrokeColor : "#fff",
        pointHighlightFill : "#fff",
        pointHighlightStroke : "rgba(100, 222, 100,1)",
        data: values
      }]};

      console.log($(canvas.closest('li')[0]).innerWidth());
      console.log($(canvas.closest('li')[0]).innerHeight());

      // Getting context.
      var ctx = canvas[0].getContext("2d");
      // Recalculate canvas size.
      canvas.width = $(canvas.closest('li')[0]).innerWidth();
      canvas.height = $(canvas.closest('li')[0]).innerHeight();

      // Drawing chart.
      var chart = new Chart(ctx).Line(chartData, chartOptions);

  }
  </script>

  @append

