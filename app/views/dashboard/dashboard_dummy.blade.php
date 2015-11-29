@extends('meta.base-user')

@section('navbar')
  @include('meta.navbar-dashboard')
@overwrite

@section('pageTitle')
  Dashboard
@stop

@section('pageStylesheet')
@stop

@section('pageContent')
  
  <div class="menu">
    <div class="menu-group">

      @foreach ($dashboardList as $iDashboard)
        <a href="{{ route('dashboard.dashboard', $iDashboard['id']) }}" class="menu-item @if ($iDashboard['active']) active @endif">
          {{ $iDashboard['name'] }}

          {{-- IF NOTIFICATION (for new shared widget) --}}
            {{-- <span class="badge" data-toggle="tooltip" data-placement="right" title="New widgets have been shared with you">
              <i class="fa fa-lightbulb-o"></i>
            </span> --}}
          {{-- ENDIF --}}

          @if ($iDashboard['active'])
            @foreach ($dashboard['widgets'] as $widget)
              @if (array_key_exists('statUrl', $widget['meta']['urls']))
                <a href="{{ $widget['meta']['urls']['statUrl'] }}" class="menu-item menu-subitem">
                  {{ $widget['meta']['general']['name']; }} 
                </a> <!-- /.menu-subitem -->
              @endif
            @endforeach
          @endif
      </a> <!-- /.menu-item -->
      @endforeach

    </div> <!-- /.menu-group -->

    <div class="granularity-menu">
      <p>Set your velocity:</p>

      <div class="granularity-selector">

        @foreach (SiteConstants::getVelocities() as $velocityName => $velocityId)
          <a href="#" class="granularity-button @if($dashboard['velocity'] == $velocityId)active @endif" data-velocity="{{ $velocityId }}">
            {{ $velocityName }}
          </a> 
        @endforeach

      </div> <!-- /.granularity-selector -->

    </div> <!-- /.granularity-menu -->
  
  </div> <!-- /.menu -->
  
  <div id="gridster-{{ $dashboard['id'] }}" class="gridster grid-base fill-height not-visible" data-dashboard-id="{{ $dashboard['id'] }}">

    {{-- Generate all the widgdets --}}
    <div class="gridster-container">

      @foreach ($dashboard['widgets'] as $widget)

        @include('widget.widget-general-layout', ['widget' => $widget['templateData']])

      @endforeach

    </div> <!-- /.gridster-container -->

  </div> <!-- /.gridster -->


@if (GlobalTracker::isTrackingEnabled() and Input::get('tour'))
  @include('dashboard.dashboard-google-converison-scripts')
@endif


@stop

@section('pageScripts')
  <!-- FDJSlibs merged -->
  {{ Minify::javascriptDir('/lib/general') }}
  {{ Minify::javascriptDir('/lib/layouts') }}
  {{ Minify::javascriptDir('/lib/widgets') }}
  <!-- FDJSlibs merged -->
  
  <!-- Gridster scripts -->
  @include('dashboard.dashboard-gridster-scripts')
  <!-- /Gridster scripts -->

  <!-- Hopscotch scripts -->
  @include('dashboard.dashboard-hopscotch-scripts')
  <!-- /Hopscotch scripts -->

  @if (GlobalTracker::isTrackingEnabled() and Input::get('tour'))
  <!-- Send acquisition event -->
  <script type="text/javascript">
    trackAll('lazy', {'en': 'Acquisition goal | Finished SignupWizard', 'el': '{{ Auth::user()->email }}', });
  </script>
  <!-- /Send acquisition event -->
  @endif

  <!-- Init FDGlobalChartOptions -->
  <script type="text/javascript">
      new FDGlobalChartOptions({data:{page: 'dashboard'}}).init();
  </script>
  <!-- /Init FDGlobalChartOptions -->

  <!-- Dashboard etc scripts -->
  <script type="text/javascript">

    function showShareModal(widgetId) {
     $('#share-widget-modal').modal('show');
     $('#share-widget-modal').on('shown.bs.modal', function (params) {
        $("#widget-id").val(widgetId);
        $('#email-addresses').focus()
      });
    }

    $(document).ready(function () {
      @if (Auth::user()->hasUnseenWidgetSharings())
        easyGrowl('info', 'You have unseen widget sharing notifications. You can check them out <a href="{{route('widget.add')}}" class="btn btn-xs btn-primary">here</a>.', 5000)
      @endif
      // Share widget submit.
      $('#share-widget-form').submit(function(event) {
        event.preventDefault();
        var emailAddresses = $('#email-addresses').val();
        var widgetId = $('#widget-id').val();

        if (emailAddresses.length > 0 && widgetId > 0) {
          $.ajax({
            type: "post",
            data: {'email_addresses': emailAddresses},
            url: "{{ route('widget.share', 'widget_id') }}".replace("widget_id", widgetId),
           }).done(function () {
            /* Ajax done. Widget shared. Resetting values. */
            $('#email-addresses-group').removeClass('has-error');
            $("#share-widget-modal").modal('hide');

            /* Resetting values */
            $('#email-addresses').val('');
            $('#widget-id').val(0);

            easyGrowl('success', "You successfully shared the widget.", 3000);
           });
          return
        } else {
          $('#email-addresses-group').addClass('has-error');
        }

      });
    });
  </script>
  <!-- /Dashboard etc scripts -->
@append

