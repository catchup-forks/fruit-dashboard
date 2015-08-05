@extends('meta.base-user')

  @section('pageTitle')
    Add widget
  @stop

  @section('pageStylesheet')
  @stop

  @section('pageContent')
  <div class="container">

    <h1 class="text-center text-white drop-shadow">
      Add widgets to your dashboard
    </h1>

    <div class="row">
      <div class="col-md-10 col-md-offset-1">
        <div class="panel panel-default panel-transparent">
          <div class="panel-body">

            <div class="row">
              <div class="col-md-5">

                <input id="filter" type="text" class="form-control margin-top-sm" autofocus="autofocus" placeholder="Type to filter, click to select" />
                
                <div id="widgets-list" class="list-group margin-top-sm">

                  @foreach($widgetDescriptors as $descriptor)
                    <a href="#" id="descriptor-{{ $descriptor->id }}" class="list-group-item changes-image" data-widget="widget-{{ $descriptor->type }}">
                      {{ $descriptor->name }}
                    </a>
                  @endforeach

                </div> <!-- /.list-group -->
              </div> <!-- /.col-md-5 -->

              <div class="col-md-7">
                <div class="row">
                  <div class="col-md-12 text-center">
                    <h3 id="descriptor-name">Click to select a widget</h3>
                    {{ HTML::image('img/demonstration/widget-empty.png', 'The Clock Widget', array('id' => 'img-change', 'class' => 'img-responsive img-rounded')) }}
                  </div> <!-- /.col-md-12 -->
                </div> <!-- /.row -->
                <div class="row">
                  <div class="col-md-12 text-center">
                    <p id="descriptor-description">
                      Then add to one of your dashboards.
                    </p>
                  </div> <!-- /.col-md-12 -->
                </div> <!-- /.row -->
                <div class="row">
                  <div class="col-md-12">
                    
                    <form>
                      
                      <div class="form-group">
                        <label for="addToDashboard">Add to dashboard:</label>
                        <select class="form-control">
                          
                          @foreach( Auth::user()->dashboards->all() as $dashboard )
                          <option>{{ $dashboard->name }}</option>
                          @endforeach

                        </select>

                      </div> <!-- .form-group -->
                      
                      <div class="form-actions text-center">
                        <a id="descriptor-add-link" href="#" class="btn btn-primary pull-right disabled">Add</a>
                      </div> <!-- /.form-actions -->

                    </form>
                    
                  </div> <!-- /.col-md-12 -->
                </div> <!-- /.row -->
              </div> <!-- /.col-md-7 -->
            </div> <!-- /.row -->

          </div> <!-- /.panel-body -->
        </div> <!-- /.panel -->
      </div> <!-- /.col-md-8 -->
    </div> <!-- /.row -->
  </div> <!-- /.container -->

  @stop
  @section('pageScripts')

  <script type="text/javascript">
    $(document).ready(function () {

      var baseUrl = "/img/demonstration/";
      var ext = ".png";

      function getID(element) {
        return element.id.substr(element.id.indexOf('-') + 1)
      }

      // Shows the descriptors for a widget
      function showDescription(descriptorID) {

        // Check for the disabled button and enable on the first selection.
        if ($('#descriptor-add-link').hasClass('disabled')) {
          $('#descriptor-add-link').removeClass('disabled');
        };

        // Gets the relevant descriptors by ID.
         $.ajax({
           type: "POST",
           data: {'descriptorID': descriptorID},
           url: "{{ route('widget.get-descriptor') }}"
          }).done(function( data ) {
            $("#descriptor-name").html(data['name']);
            $("#descriptor-description").html(data['description']);
            $("#descriptor-add-link").attr("href", "{{ URL::route('widget.doAdd', 'descriptor_id') }}".replace("descriptor_id", descriptorID));
            $('#img-change').attr('src', baseUrl + 'widget-' + data['type'] + ext);
            $('#img-change').attr('alt', "The " + data['name']);
          });
      }

      // Filter the list items and show descriptors if only one element is left
      $("#filter").keyup(function () {
        var filter = $(this).val();
        var count = 0;
        var last = null;
        $("#widgets-list a").each(function () {
          if ($(this).text().search(new RegExp(filter, "i")) < 0) {
            $(this).fadeOut();
          } else {
            count++;
            last = this;
            $(this).show();
          }
        });
        if (count == 1) {
          showDescription(getID(last));
        }

      });

      // Show the descriptors when the list item is clicked.
      $('.changes-image').click(
        function(e) {
          e.preventDefault();
          showDescription(getID(this));
        });

    });
  </script>
  @append
