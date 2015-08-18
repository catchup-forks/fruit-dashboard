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

        @foreach (Auth::user()->dashboards as $dashboard)

          <li data-target="#dashboards" data-slide-to="{{ $dashboard->number }}" data-toggle="tooltip" data-placement="top" title="{{ $dashboard->name }}" class="drop-shadow @if($dashboard->is_default) active @endif"></li>

        @endforeach

      </ol>

      {{-- Make a wrapper for dashboards --}}
      <div class="carousel-inner">

        @foreach (Auth::user()->dashboards as $dashboard)

          <div class="item @if($dashboard->is_default) active @endif">

            <div class="fill" @if(Auth::user()->background->is_enabled) style="background-image:url({{ Auth::user()->background->url }});" @endif>
            </div> <!-- /.fill -->

            {{-- Here comes the dashboard content --}}
            <div id="gridster-{{ $dashboard->number }}" class="gridster grid-base fill-height">

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

      @if (Auth::user()->dashboards->count() > 1)
      {{-- Set the navigational controls on sides. --}}
      <a class="left carousel-control" href="#dashboards" data-slide="prev">
          <span class="icon-prev"></span>
      </a>
      <a class="right carousel-control" href="#dashboards" data-slide="next">
          <span class="icon-next"></span>
      </a>
      @endif

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
    @foreach (Auth::user()->dashboards as $dashboard)

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

  </script>

  @append

