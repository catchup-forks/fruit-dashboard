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

                    <a href="#{{ $group['name'] }}" class="list-group-item" data-redirect-url="{{ ($group['connect_route']) ? route($group['connect_route']) : "" }}" data-connected="{{ Auth::user()->isServiceConnected($group['name']) }}" data-selection="group" data-group="{{ $group['name'] }}" data-type="{{ $group['type'] }}">
                      <span class="service-name">{{ $group['display_name'] }}</span>
                      {{-- This is the span for the selection icon --}}
                      <span class="selection-icon"> </span>
                    </a>

                  @endforeach

                  @if (count(Auth::user()->getWidgetSharings()) > 0)
                    <a href="#shared_widgets" class="list-group-item list-group-item-info" data-selection="group" data-group="shared_widgets" data-type="shared">
                        Shared widgets
                        <span class="selection-icon"> </span>
                    </a>
                  @endif

                </div> <!-- /.list-group -->

                <div class="alert alert-info alert-dismissible" role="alert">
                  Can't find the service you were looking for?
                  <strong><a href="https://fruitdashboard.uservoice.com" target="_blank">Tell us</a>.</strong>
                </div> <!-- /.alert -->


              </div> <!-- /.col-md-3 -->
              <!-- / category list-group -->

              <!-- widget list-group -->
              <div class="col-md-4">

                <h3 class="text-center">Select a widget</h3>

                <div class="list-group margin-top-sm not-visible">
                  @foreach(Auth::user()->getWidgetSharings() as $sharing)
                      <span id="descriptor-{{ $sharing->widget->getDescriptor()->id }}" class="list-group-item" data-widget="widget-{{ $sharing->widget->getDescriptor()->type }}" data-selection="widget" data-group="shared_widgets">
                        {{ $sharing->widget->getDescriptor()->name }}
                        <a href="{{ route('widget.share.reject', $sharing->id) }}" class="btn btn-danger btn-xs has-margin-horizontal-sm pull-right">Reject </a>
                        {{-- This is the span for the selection icon --}}
                    </span>
                  @endforeach

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
                <div class="col-md-5">

                  @foreach(SiteConstants::getWidgetDescriptorGroups() as $group)

                    @foreach($group['descriptors'] as $descriptor)

                      <div data-descriptor-type="widget-{{$descriptor->type}}" data-descriptor-id="{{$descriptor->id}}" class="descriptors not-visible">
                          <div class="row">
                            <div class="col-md-12">

                                <h3 class="descriptor-name text-center">{{ $descriptor->name }}
                                </h3> <!-- /.descriptor-name -->
                                {{ HTML::image($descriptor->getPhotoLocation(), $descriptor->name, array(
                                    'class' => 'opaque img-responsive img-rounded center-block'
                                ))}}

                            </div> <!-- /.col-md-12 -->
                          </div> <!-- /.row -->

                          <div class="row">
                            <div class="col-md-12">
                              <p id="" class="lead margin-top-sm descriptor-description">{{ $descriptor->description }}</p>
                              <hr>
                            </div> <!-- /.col-md-12 -->
                          </div> <!-- /.row -->

                      </div> <!-- /.descriptors -->

                    <!-- / widget description col -->

                    @endforeach

                  @endforeach

                <!-- action panel -->
                <div class="row">
                  <div id="add-widget" class="col-md-12 not-visible">

                        {{ Form::open(array(
                            'id' => 'add-widget-form',
                            'action' => 'widget.add')) }}

                          <div class="form-group">
                            <div id="add_to_dashboard_select">
                              <label for="addToDashboard">Add to dashboard:</label>
                              <select name="toDashboard" class="form-control">

                                @foreach( Auth::user()->dashboards->all() as $dashboard )
                                <option value="{{ $dashboard->id }}">{{ $dashboard->name }}</option>
                                @endforeach
                                <option value='0'>Create a new dashboard</option>

                              </select>
                            </div>
                          </div>
                          <div class="form-group">
                            <div class="hidden" id="add_new_dashboard_input">
                              <label for="newToDashboard">Please enter the name of the new dashboard:</label>
                              <input type="text" name="newDashboardName" value='' placeholder='New dashboard' class="form-control">
                            </div>
                          </div> <!-- .form-group -->

                          <div class="form-actions pull-right">
                            <a href="{{ URL::route('dashboard.dashboard') }}" class="btn btn-link">Cancel</a>

                            {{ Form::submit('Add' , array(
                              'id' => 'add-widget-submit-button',
                              'class' => 'btn btn-primary' )) }}

                          </div> <!-- /.form-actions -->

                        {{ Form::close() }}

                    </div> <!-- /#add-widget .col-md-12 -->

                    <div id="connect-service" class="col-md-12 text-center not-visible">
                      <div class="alert alert-warning" role="alert">
                        <strong>
                          <span class="fa fa-exclamation-triangle"></span>
                        </strong>
                        You have to connect this service first.</div>

                      <div class="form-actions pull-right">

                      <a href="{{ URL::route('dashboard.dashboard') }}" class="btn btn-link">Cancel</a>

                      <a id="connect-widget-submit" href="#connect" class="btn btn-primary">Connect</a>

                      </div> <!-- /.form-actions -->

                    </div> <!-- /#connect-service .col-md-12 -->

                </div> <!-- /.row -->
                <!-- / action panel -->

                </div> <!-- /.col-md-5 -->

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
      var url;

      // Select the first available group and filter the widgets.
      $('[data-selection="group"]').find('.selection-icon').first().toggleClass('fa fa-check text-success pull-right');
      filterWidgets($('[data-selection="group"]').first().data('group'));

      selectFirstWidgetFromGroup($('[data-selection="group"]').first().data('group'));

      setTimeout(showActions, 100);

      // Filter widgets by group.
      function filterWidgets(group) {
        // Look for .not-visible wrapper and remove if any.
        $('.list-group.not-visible').removeClass('not-visible');
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

        showWidgetDescription(firstAvailableWidget.first().data('widget'));
      }

      // Remove previously added checkmarks from context.
      function removeSelectionFromContext(context) {
        $('[data-selection="' + context + '"] > .selection-icon').attr('class', 'selection-icon');
      }

      // Shows the relevant widget descriptors.
      function showWidgetDescription(descriptorType) {

        $('[data-descriptor-type]').hide();
        $('[data-descriptor-type="' + descriptorType + '"]').removeClass('not-visible').show();

        // Change the form url
        descriptorID = $('[data-descriptor-type="' + descriptorType + '"]').attr('data-descriptor-id')
        descriptorUrl = "{{ route('widget.doAdd', 'descriptorID') }}".replace('descriptorID', descriptorID);
        $('#add-widget-form').attr('action', descriptorUrl)
      }

      // Show the available actions pane.
      function showActions() {
        var addPanel = $('#add-widget');
        var connectPanel = $('#connect-service');

        var firstCheckedGroup = $('.fa-check').first().parent();
        //var groupConnectionMarker = firstCheckedGroup.find('span').first();

        url = firstCheckedGroup.data('redirect-url');

        addPanel.addClass('not-visible');
        connectPanel.addClass('not-visible');

        if (firstCheckedGroup.data('type') === 'service' && firstCheckedGroup.data('connected').length==0) {
          connectPanel.removeClass('not-visible');
        } else {
          addPanel.removeClass('not-visible');
        }

      }

      // Select or deselect list-group-items by clicking.
      $('.list-group-item').click(function(e){
        // Stop the jump to behaviour.
        if ( ! $(e.target).is('a')) {
          e.preventDefault();
        }

        // Get context (group or widget).
        var context = $(this).data('selection');

        // If a group was clicked, filter widgets.
        if (context == "group") {
          var group = $(this).data('group');

          filterWidgets(group);
          selectFirstWidgetFromGroup(group);

          setTimeout(showActions, 100);

        // If a widget was clicked, show descriptions.
        } else if (context == "widget") {
          showWidgetDescription($(this).data('widget'));
        } else {
          return false
        };

        removeSelectionFromContext(context);

        // Add checkmark to the clicked one.
        $(this).find('.selection-icon').first().toggleClass('fa fa-check text-success pull-right');

      });

      // Listen clicks on the #connect-widget-submit button.
      // Displays modal and redirects to connect service page.
      $('#connect-widget-submit').click(function(e){
        var service = '';
        $('.service-name').each(function(index, element){
          if($(element).next().hasClass('fa-check')) {
            service = $(element).html();
          }
        });
        e.preventDefault();
        bootbox.dialog({
          title: 'We need you to allow Fruit Dashboard access.',
          message: 'To connect ' + service + ', we will redirect you to their site.',
          buttons: {
            cancel: {
              label: 'Cancel',
              className: 'btn-default',
              callback: function(){}
            },
            main: {
              label: 'Take me to ' + service,
              className: 'btn-primary',
              callback: function(result) {
                if (result) {
                  // Using from extension, redirect in new tab
                  if (window!=window.top) {
                    window.open(url, '_blank');
                  // Using website, redirect on same tab
                  } else {
                    $("#add-widget-form").submit();
                  }
              }
              }
            }  
          }
        });
      });

      /**
       * @listens | element(s): $('#add_to_dashboard_select > select') | event:change
       * --------------------------------------------------------------------------
       * Changes the select to a text input when 'add to new dashboard' is selected
       * --------------------------------------------------------------------------
       */
       $('#add_to_dashboard_select > select').change(function() {
          // Get items
          inputDiv  = $('#add_new_dashboard_input');

          // Switch items
          if ($(this).find("option:selected").val() == 0) {
            inputDiv.removeClass('hidden').fadeIn();
            inputDiv.find('input').focus();
          } else {
            inputDiv.fadeOut();
          }
       });

      /**
       * @listens | element(s): $('#add-widget-submit-button') | event:click
       * --------------------------------------------------------------------------
       * Changes the button text to 'Loading...' when clicked
       * --------------------------------------------------------------------------
       */
       $('#add-widget-submit-button').click(function() {
          $(this).button('loading');
       });

      /**
       * @listens | element(s): $('#add_to_dashboard_select > select') | event:change
       * --------------------------------------------------------------------------
       * Changes the select to a text input when 'add to new dashboard' is selected
       * --------------------------------------------------------------------------
       */
       $('#add-widget-form').submit(function(e) {
          // Get items
          selectDiv = $('#add_to_dashboard_select');
          inputDiv  = $('#add_new_dashboard_input');

          // Check if the user added a name for the new dashboard
          if ((selectDiv.find(':selected').val() == 0) &&
              !(inputDiv.find('[name=newDashboardName]').val())) {
            // Don't send submit and show error message
            e.preventDefault();
            easyGrowl('error', "Please enter a name for the new dashboard", 3000);
          }
       });
    });
  </script>
  @append
