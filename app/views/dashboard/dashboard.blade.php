@extends('meta.base-user')

  @section('pageTitle')
    Dashboard
  @stop

  @section('pageStylesheet')
  @stop

  @section('pageContent')

  <!-- add new widget -->
  <div>
    <a href="{{ URL::route('connect.connect') }}">
      <i class="dropdown-icon fa fa-2x fa-plus" id="addNewWidget" alt="Add new widget" title="Add new widget"></i>
    </a>
  </div>

  <!-- widget list -->
  <div class="container">
    <div class="row">
      <div class="gridster not-visible">
        <ul>
          @for ($i = 0; $i < count($allFunctions); $i++)

            @include('dashboard.widget', ['widget_data' => $allFunctions[$i]])

          @endfor
        </ul>
      </div>
    </div>
  </div>
  <!-- /widget list -->

  <!-- Modals -->
  <div id="modal-signin-signup" class="modal fade" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-md">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
          <h4 class="modal-title">Subscribe!</h4>
        </div>
        <div class="modal-body">
          If you want to customize your dashboard, please sign in or sign up.
        </div>
        <div class="modal-footer">
          <a class="btn btn-success" href="{{ URL::route('auth.signin') }}">Sign in</a>
          <a class="btn btn-success" href="{{ URL::route('auth.signup') }}">Sign up</a>
        </div>
      </div> <!-- / .modal-content -->
    </div> <!-- / .modal-dialog -->
  </div>
  <!-- /Modals -->

  @stop

  @section('pageScripts')

    <!-- Grid functions -->
    <script type="text/javascript">
     $(document).ready(function() {
      var gridster;
      var positioning = [];
      var widget_width = $(window).width()/12-15;
      var widget_height = $(window).height()/12-20;

      $(function(){
        gridster = $(".gridster ul").gridster({
          /* widget_base_dimenions - finer resizable steps*/
          widget_base_dimensions: [widget_width, widget_height],
          widget_margins: [5, 5],
          helper: 'clone',
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
            stop: function(e, ui, $widget) {
              positioning = gridster.serialize();
              positioning = JSON.stringify(positioning);
              $.ajax({
                type: "POST",
                url: "/widgets/save-position/{{Auth::user()->id}}/" + positioning
              });
            }
          },
          draggable: {
            stop: function(e, ui, $widget) {
              positioning = gridster.serialize();
              positioning = JSON.stringify(positioning);
              $.ajax({
                type: "POST",
                url: "/widgets/save-position/{{Auth::user()->id}}/" + positioning
              });
            }
          }  
        }).data('gridster');
      });
     });
    </script>
    <!-- /Grid functions -->

    {{ HTML::script('js/jquery.color.js') }}
    {{ HTML::script('js/jquery.easing.1.3.js') }}

    @if (Auth::user()->id == 1)
    <script type="text/javascript">
    // Grid event helpers
    // if user is not logged in
    init.push(function(){
      $('.gs-close-widgets').on('click', function(event){
        event.preventDefault();
        $('#modal-signin-signup').modal('show');
        return false;
      });
    });
   
    </script>
    @endif

    <!-- Saving text and settings -->
    <script type="text/javascript">
      $(document).ready(function(){
                  
        function sendText(ev) {
          var text = $(ev.target).val() ? $(ev.target).val() : '';
          text = text.replace(/\n\r?/g, '[%LINEBREAK%]');
          var id = $(ev.target).attr('id');
          
          $.ajax({
            type: 'POST',
            url: '/widgets/save-text/' + id + '/' + text
          });
        }

        function saveWidgetName(ev) {
          
          var input = $(ev.target).parent().parent().children('input');
          var newName = input.val();
          var id = input.attr('id');

          if (newName) {
            $.ajax({
              type: 'POST',
              url: '/widgets/settings/name/' + id + '/' + newName,
              success:function(message,code){
                var current = input.css('background-color');

                input.animate({'background-color':'LightGreen'},50,'easeInCirc');
                input.animate({'background-color': current},100,'easeOutCirc');
              }
            });            
          }
        }

        // user finished typing
        $('.save-widget-name').click(saveWidgetName);
        $('.note').keyup(_.debounce(sendText,500));
        
      });
    </script>
    <!-- /Saving text -->

    <!-- script for clock -->
    <script type="text/javascript">
      $(document).ready(function()
      {
        function startTime() {
          var today = new Date();
          var h = today.getHours();
          var m = today.getMinutes();
          m = checkTime(m);
          $('.digitTime').html(h + ':' + m);
          var t = setTimeout(function(){startTime()},500);
        }

        function checkTime(i) {
          if (i<10){i = "0" + i};  // add zero in front of numbers < 10
          return i;
        }

        startTime();

        $('.not-visible').fadeIn(500);
      });
    </script>
    <!-- /script for clock -->
    
    <!-- Deciding on proper greeting -->
    <script type="text/javascript">
      $(document).ready(function()
      {
        var hours = new Date().getHours();
        
        if(17 <= hours || hours < 5) { $('.greeting').html('evening'); }
        if(5 <= hours && hours < 13) { $('.greeting').html('morning'); }
        if(13 <= hours && hours < 17) { $('.greeting').html('afternoon'); } 
      });
    </script>
    <!-- /Deciding on proper greeting -->

    <!-- fittext -->
    <script type="text/javascript">
      $(document).ready(function()
      {

        $("#digitClock").bind('resize', function(e){
          $("h1.digitTime").fitText(0.3);
        })

        $("#textWidgetId").bind('resize', function(e){
          $("p.textWidgetClass").fitText(0.8);
        })
      });
    </script>
    <!-- /fittext -->

    <!-- chart.js options -->
    <script type="text/javascript">

    var options = {
      responsive: false,
      maintainAspectRatio: false,
      showScale: false,
      showTooltips: false,
      pointDot: false,
      tooltipXOffset: 0
    };

    var data, ctx;

    @for ($i = 0; $i < count($allFunctions); $i++)
      @if ($allFunctions[$i]['widget_type']=='google-spreadsheet-line-column')

      /* {{ $allFunctions[$i]['statName'] }} */

      data = {
        labels: [@foreach ($allFunctions[$i]['history'] as $date => $value)"", @endforeach],
        datasets: [
            {
                label: "{{ $allFunctions[$i]['statName'] }}",
                fillColor: "rgba(151,187,205,0.4)",
                strokeColor: "rgba(151,187,205,0.6)",
                data: [
                  @foreach ($allFunctions[$i]['history'] as $date => $value)
                    @if (is_numeric($value))
                      @if($value == null)
                        0,
                      @else
                        {{ $value }},
                      @endif 
                    @else
                        '{{ $value }}',
                    @endif
                  @endforeach]
            }
        ]
      };

      ctx = $("#chart{{$allFunctions[$i]['widget_id']}}").get(0).getContext("2d");
      var Chart{{$allFunctions[$i]['widget_id']}} = new Chart(ctx).Line(data, options);

      /* / {{ $allFunctions[$i]['statName'] }} */

      @endif

    @endfor

    </script>
    <!-- /chart.js options -->

  @append

