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
      <div class="col-md-12">
        <div class="panel panel-default panel-transparent">
          <div class="panel-body">

            <div class="row">

              <!-- category list-group -->
              <div class="col-md-3">

                <h3 class="text-center">Select a group</h3>

                <div class="list-group margin-top-sm">
                  
                  @foreach(SiteConstants::getWidgetDescriptorGroups() as $group)
                    
                    <a href="#{{ $group['name'] }}" class="list-group-item" data-selection="group" data-group="{{ $group['name'] }}">
                      {{ $group['display_name'] }}
                      {{-- This is the span for the selection icon --}}
                      <span class="selection-icon"> </span>
                    </a>

                  @endforeach

                </div> <!-- /.list-group -->

                <div class="alert alert-info alert-dismissible" role="alert">
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                  Can't find the service you were looking for?
                  <strong><a href="https://fruitdashboard.uservoice.com" target="_blank">Tell us</a>.</strong>
                </div> <!-- /.alert -->
                

              </div> <!-- /.col-md-3 -->
              <!-- / category list-group -->

              <!-- widget list-group -->
              <div class="col-md-4">
                
                <h3 class="text-center">Select a widget</h3>

                <div class="list-group margin-top-sm">
                  
                  @foreach(SiteConstants::getWidgetDescriptorGroups() as $group)

                    @foreach($group['descriptors'] as $descriptor)

                        <a href="#" id="descriptor-{{ $descriptor->id }}" class="list-group-item" data-widget="widget-{{ $descriptor->type }}" data-selection="widget" data-group="{{ $group['name'] }}">
                          {{ $descriptor->name }}
                          {{-- This is the span for the selection icon --}}
                          <span class="selection-icon"> </span>
                        </a>
                    @endforeach

                  @endforeach

                </div> <!-- /.list-group -->
              </div> <!-- /.col-md-4 -->
              <!-- / widget list-group -->

              <!-- widget description col -->
              <div id="descriptors" class="col-md-5 not-visible">
                
                <div class="row">
                  <div class="col-md-12">
                    <h3 id="descriptor-name" class="text-center">Add widget to dashboard</h3>
                    {{ HTML::image('img/demonstration/widget-empty.png', 'The Clock Widget', array('id' => 'img-change', 'class' => 'img-responsive img-rounded center-block')) }}
                  </div> <!-- /.col-md-12 -->
                </div> <!-- /.row -->
                
                <div class="row">
                  <div class="col-md-12">
                    <p id="descriptor-description" class="lead margin-top-sm">Select a widget to add it to one of your dashboards.</p>
                    <hr>
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

              </div> <!-- /#descriptors .col-md-5 -->
              <!-- / widget description col -->


            </div> <!-- /.row -->              
          </div> <!-- /.panel-body -->
        </div> <!-- /.panel -->
      </div> <!-- /.col-md-12 -->
    </div> <!-- /.row -->
  </div> <!-- /.container -->     


  @stop
  @section('pageScripts')

  <script type="text/javascript">
    $(document).ready(function () {

      var baseUrl = "/img/demonstration/";
      var ext = ".png";

      // Select the first available group and filter the widgets.
      $('[data-selection="group"]').find('span').first().toggleClass('fa fa-check text-success pull-right');
      filterWidgets($('[data-selection="group"]').first().data('group'));

      selectFirstWidgetFromGroup($('[data-selection="group"]').first().data('group'));
      
      // Filter widgets by group.
      function filterWidgets(group) {
        // Hide all widget list-group-items.
        $('[data-selection="widget"]').hide();
        // Show the filtered list-group-items.
        $('[data-group="' + group + '"]').show();
      }

      // Select the first available widget and show description.
      function selectFirstWidgetFromGroup(group) {

        removeSelectionFromContext("widget");

        var firstAvailableWidget = $('[data-group="' + group + '"][data-selection="widget"]');

        firstAvailableWidget.find('span').first().toggleClass('fa fa-check text-success pull-right');
        showWidget(firstAvailableWidget.attr('id').substring(firstAvailableWidget.attr('id').indexOf('-') + 1));
      }

      // Remove previously added checkmarks from context.
      function removeSelectionFromContext(context) {
        $('[data-selection="' + context + '"] > .selection-icon').attr('class', 'selection-icon');
      }

      function getID(element) {
        return element.id.substr(element.id.indexOf('-') + 1)
      }

      // Gets the relevant descriptors by ID.
      function showWidget(descriptorID) {
        
        // Fade out the wrapper
        $('#descriptors').fadeOut();

        // Start AJAX call
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
            $('#descriptors').fadeIn();
            $('#add-widget-submit-button').removeClass('disabled');
          });

      }

      // Select or deselect list-group-items by clicking.
      $('.list-group-item').click(function(e){
        // Stop the jump to behaviour.
        e.preventDefault();

        // Get context (group or widget).
        var context = $(this).data('selection');

        // If a group was clicked, filter widgets.
        if (context == "group") {
          var group = $(this).data('group');
          filterWidgets(group);
          selectFirstWidgetFromGroup(group);
        // If a widget was clicked, show descriptions.
        } else if (context == "widget") {
          var descriptorID = getID(this);
          showWidget(descriptorID);
        } else {
          return false
        };

        removeSelectionFromContext(context);

        // Add checkmark to the clicked one.
        $(this).find('span').first().toggleClass('fa fa-check text-success pull-right');
        
      });


    });
  </script>
  @append
