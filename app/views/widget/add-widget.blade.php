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

                <!-- Category tabs -->
                <div class="row">
                  <ul id="categoryTabs" class="nav nav-tabs" role="tablist">
                    @foreach(SiteConstants::getWidgetDescriptorGroups() as $group)
                      <li role="presentation" class=""><a href="#{{ $group['name'] }}" aria-controls="home" role="tab" data-toggle="tab">{{ $group['display_name'] }}</a></li>
                    @endforeach
                  </ul>
                </div>
                <!-- Category tabs -->

                <div id="widgets-list" class="list-group margin-top-sm">

                  <!-- Category widgets -->
                  <div class="tab-content">
                    @foreach(SiteConstants::getWidgetDescriptorGroups() as $group)

                      <!-- Category panel -->
                      <div role="tabpanel" class="tab-pane fade in active" id="{{ $group['name'] }}">

                        <!-- Widgets -->
                        @foreach($group['descriptors'] as $descriptor)
                          <a href="#" id="descriptor-{{ $descriptor->id }}" class="list-group-item changes-image" data-widget="widget-{{ $descriptor->type }}">
                            {{ $descriptor->name }}
                            {{-- This is the span for the selection icon --}}
                            <span class="selection-icon"> </span>

                            {{-- If user is on free plan display labels --}}
                            @if (Auth::user()->subscription->isOnFreePlan())
                              @if ($descriptor->is_premium == 0)
                                <span class="label label-success pull-right">Free</span>
                              @else
                                <span class="label label-default pull-right">Premium</span>
                              @endif
                            @endif
                          </a>
                        @endforeach
                        <!-- /Widgets -->

                      </div>
                      <!-- /Category panel -->

                    @endforeach
                  </div>
                  <!-- Category widgets -->


                </div> <!-- /.list-group -->
                <a href="{{ route('dashboard.dashboard') }}"><button class="btn btn-warning">Cancel</button></a>
              </div> <!-- /.col-md-5 -->

              <div class="col-md-7">
                <div class="row">
                  <div class="col-md-12 text-center">
                    <h3 id="descriptor-name">Click to select a widget</h3>
                    {{ HTML::image('img/demonstration/widget-empty.png', 'The Clock Widget', array('id' => 'img-change', 'class' => 'img-responsive img-rounded center-block')) }}
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

                    {{ Form::open(array(
                        'id' => 'add-widget-form',
                        'action' => 'widget.add')) }}

                      <div class="form-group">
                        <label for="addToDashboard">Add to dashboard:</label>
                        <select name="toDashboard" class="form-control">

                          @foreach( Auth::user()->dashboards->all() as $dashboard )
                          <option value="{{ $dashboard->id }}">{{ $dashboard->name }}</option>
                          @endforeach

                        </select>

                      </div> <!-- .form-group -->

                      <div class="form-actions text-center">
                      {{ Form::submit('Add' , array(
                          'id' => 'add-widget-submit-button',
                          'class' => 'btn btn-primary pull-right disabled' )) }}

                      </div> <!-- /.form-actions -->

                    {{ Form::close() }}

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

      // Select first tab by default
      $('#categoryTabs a:first').tab('show')

      var baseUrl = "/img/demonstration/";
      var ext = ".png";

      function getID(element) {
        return element.id.substr(element.id.indexOf('-') + 1)
      }

      // Shows the descriptors for a widget
      function showDescription(descriptorID) {

        // Check for the disabled button and enable on the first selection.
        if ($('#add-widget-submit-button').hasClass('disabled')) {
          $('#add-widget-submit-button').removeClass('disabled');
        };

        // Gets the relevant descriptors by ID.
         $.ajax({
           type: "POST",
           data: {'descriptorID': descriptorID},
           url: "{{ route('widget.get-descriptor') }}"
          }).done(function( data ) {
            $("#descriptor-name").html(data['name']);
            $("#descriptor-description").html(data['description']);
            $("#add-widget-form").attr("action", "{{ URL::route('widget.doAdd', 'descriptor_id') }}".replace("descriptor_id", descriptorID));
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

	  $('.changes-image').click(function(e) {
        e.preventDefault();
        showDescription(getID(this));
      });

    // Select or deselect items by clicking
    $('.list-group-item').click(function(){
      // remove previously added checkmarks
      $('.selection-icon').attr('class', 'selection-icon');
      // add checkmark to the actual one
      $(this).find('span').first().toggleClass('fa fa-check text-success pull-right');
    });

    });
  </script>
  @append
