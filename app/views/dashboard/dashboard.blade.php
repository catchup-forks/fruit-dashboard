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

<div id="dashboards" class="carousel slide">

    @if (count($dashboards) > 1)
      {{-- Include navigation dots for each dashboard. --}}
      <ol class="carousel-indicators">

        @foreach ($dashboards as $dashboardId => $dashboardMeta)
          {{-- Set active dashboard. Get from backend or make the default --}}
          @if (isset($activeDashboard))
            @if ($dashboardId == $activeDashboard)
              <li data-target="#dashboards" data-slide-to="{{ $dashboardMeta['count'] }}" data-toggle="tooltip" data-placement="top" title="{{ $dashboardMeta['name']}}" class="drop-shadow active"></li>
            @else
              <li data-target="#dashboards" data-slide-to="{{ $dashboardMeta['count'] }}" data-toggle="tooltip" data-placement="top" title="{{ $dashboardMeta['name']}}" class="drop-shadow"></li>
            @endif
          @else
            @if($dashboardMeta['is_default'])
              <li data-target="#dashboards" data-slide-to="{{ $dashboardMeta['count'] }}" data-toggle="tooltip" data-placement="top" title="{{ $dashboardMeta['name']}}" class="drop-shadow active"></li>
            @else
              <li data-target="#dashboards" data-slide-to="{{ $dashboardMeta['count'] }}" data-toggle="tooltip" data-placement="top" title="{{ $dashboardMeta['name']}}" class="drop-shadow"></li>
            @endif
          @endif
        @endforeach

      </ol>
    @endif

    {{-- Make a wrapper for dashboards --}}
    <div class="carousel-inner">

      @foreach ($dashboards as $dashboardId => $dashboardMeta)

          {{-- Set active dashboard. Get from backend or make the default --}}
          @if (isset($activeDashboard))
            @if ($dashboardId == $activeDashboard)
              <div class="item active">
            @else
              <div class="item">
            @endif
          @else
            @if($dashboardMeta['is_default'])
              <div class="item active">
            @else
              <div class="item">
            @endif
          @endif

          <div class="fill" @if(Auth::user()->background->is_enabled) style="background-image:url({{ Auth::user()->background->url }});" @endif>
          </div> <!-- /.fill -->

          {{-- Here comes the dashboard content --}}
          <div id="gridster-{{ $dashboardId }}" class="gridster grid-base active fill-height not-visible" data-dashboard-id="{{ $dashboardId }}">

            {{-- Generate all the widgdets --}}
            <div class="gridster-container">

              @foreach ($dashboardMeta['widgets'] as $widget)

                @include('widget.widget-general-layout', ['widget' => $widget['templateData']])

              @endforeach

            </div> <!-- /.gridster-container -->

          </div> <!-- /.gridster -->

        </div> <!-- /.item -->

      @endforeach

    </div> <!-- /.carousel-inner -->

    @if (count($dashboards) > 1)
    {{-- Set the navigational controls on sides. --}}
    <a class="left carousel-control" href="#dashboards" data-slide="prev">
        <span class="icon-prev"></span>
    </a>
    <a class="right carousel-control" href="#dashboards" data-slide="next">
        <span class="icon-next"></span>
    </a>
    @endif

</div> <!-- /#dashboards -->

<!-- Share widget-->
<div class="modal fade" id="share-widget-modal" tabindex="-1" role="dialog" aria-labelledby="share-widget-label">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="share-widget-label">Share widget</h4>
      </div>
      <form id="share-widget-form" class="form-horizontal">
        <div class="modal-body">
            <div id="email-addresses-group" class="form-group">
              <label for="new-dashboard" class="col-sm-5 control-label">Type the email address of the user you want to share the widget with.</label>
              <div class="col-sm-7">
                <input id="email-addresses" type="text" class="form-control" />
                <input id="widget-id" type="hidden" />
              </div> <!-- /.col-sm-7 -->
            </div> <!-- /.form-group -->
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Share</button>
        </div>
      </form>
    </div>
  </div>
</div>

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
    // Initialize Carousel
    $('.carousel').carousel({
      interval: false // stops the auto-cycle
    })

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

