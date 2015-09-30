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

      @foreach (Auth::user()->dashboards as $index => $dashboard)
        {{-- Set active dashboard. Get from backend or make the default --}}
        @if (isset($activeDashboard))
          @if ($dashboard->id == $activeDashboard)
            <li data-target="#dashboards" data-slide-to="{{ $index }}" data-toggle="tooltip" data-placement="top" title="{{ $dashboard->name }}" class="drop-shadow active"></li>
          @else
            <li data-target="#dashboards" data-slide-to="{{ $index }}" data-toggle="tooltip" data-placement="top" title="{{ $dashboard->name }}" class="drop-shadow"></li>
          @endif
        @else
          @if($dashboard->is_default)
            <li data-target="#dashboards" data-slide-to="{{ $index }}" data-toggle="tooltip" data-placement="top" title="{{ $dashboard->name }}" class="drop-shadow active"></li>
          @else
            <li data-target="#dashboards" data-slide-to="{{ $index }}" data-toggle="tooltip" data-placement="top" title="{{ $dashboard->name }}" class="drop-shadow"></li>
          @endif
        @endif
      @endforeach

    </ol>

    {{-- Make a wrapper for dashboards --}}
    <div class="carousel-inner">

      @foreach (Auth::user()->dashboards as $dashboard)

          {{-- Set active dashboard. Get from backend or make the default --}}
          @if (isset($activeDashboard))
            @if ($dashboard->id == $activeDashboard)
              <div class="item active">
            @else
              <div class="item">
            @endif
          @else
            @if($dashboard->is_default)
              <div class="item active">
            @else
              <div class="item">
            @endif
          @endif

          <div class="fill" @if(Auth::user()->background->is_enabled) style="background-image:url({{ Auth::user()->background->url }});" @endif>
          </div> <!-- /.fill -->

          {{-- Here comes the dashboard content --}}
          @if($dashboard->is_locked)
          <div id="gridster-{{ $dashboard->id }}" class="gridster grid-base fill-height not-visible" data-dashboard-id="{{ $dashboard->id }}" data-lock-direction="lock">
          @else
          <div id="gridster-{{ $dashboard->id }}" class="gridster grid-base fill-height not-visible" data-dashboard-id="{{ $dashboard->id }}" data-lock-direction="unlock">
          @endif  

            {{-- Generate all the widgdets --}}
            <div class="gridster-container">

              @foreach ($dashboard->widgets as $widget)

                @if ($widget->state != 'hidden')
                  @include('widget.widget-general-layout', ['widget' => $widget->getSpecific()])
                @endif

              @endforeach

            </div> <!-- /.gridster-container -->

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
              <label for="new-dashboard" class="col-sm-5 control-label">Type the email addresses of people you want to share.</label>
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

@stop

@section('pageScripts')
  <!-- FDGeneral* classes -->
  <script type="text/javascript" src="{{ URL::asset('lib/FDGridster.js')"></script>
  <script type="text/javascript" src="{{ URL::asset('lib/FDWidget.js')"></script>
  <script type="text/javascript" src="{{ URL::asset('lib/FDCanvas.js')"></script>
  <script type="text/javascript" src="{{ URL::asset('lib/FDChart.js')"></script>
  <script type="text/javascript" src="{{ URL::asset('lib/FDChartOptions.js')"></script>
  <script type="text/javascript" src="{{ URL::asset('lib/FDTable.js')"></script>
  <!-- /FDGeneral* classes -->

  <!-- FDAbstractWidget* classes -->
  <script type="text/javascript" src="{{ URL::asset('lib/widgets/FDHistogramWidget.js')"></script>
  <script type="text/javascript" src="{{ URL::asset('lib/widgets/FDTableWidget.js')"></script>
  <!-- /FDAbstractWidget* classes -->

  <!-- FDWidget* classes -->
  @foreach (WidgetDescriptor::where('category', '!=', 'hidden')->get() as $descriptor) 
    <script type="text/javascript" src="{{ URL::asset('lib/widgets/{{ $descriptor->category }}/FD{{ str_replace(' ', '', ucwords(str_replace('_',' ', $descriptor->type))) }}Widget.js')"></script>
  @endforeach
  <!-- /FDWidget* classes -->

  <!-- Gridster scripts -->
  @include('dashboard.dashboard-gridster-scripts')
  <!-- /Gridster scripts -->

  <!-- Dashboard locking scripts -->
  @include('dashboard.dashboard-locking-scripts')
  <!-- /Dashboard locking scripts -->

  <!-- Hopscotch scripts -->
  @include('dashboard.dashboard-hopscotch-scripts')
  <!-- /Hopscotch scripts -->

  <!-- Widget general scripts -->
  @include('widget.widget-general-scripts')
  <!-- /Widget general scripts -->

  <!-- Init FDChartOptions -->
  <script type="text/javascript">
      new FDChartOptions('dashboard').init();
  </script>
  <!-- /Init FDChartOptions -->

  <!-- Dashboard etc scripts -->
  <script type="text/javascript">
    // Initialize Carousel
    $('.carousel').carousel({
      interval: false // stops the auto-cycle
    })

    // Change the dashboard-lock on dashboard change
    $('.carousel').on('slid.bs.carousel', function () {
      setDashboardLock($('.item.active > .gridster').attr("data-dashboard-id"), $('.item.active > .gridster').attr("data-lock-direction") == 'lock' ? true : false, false);
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
      $('#share-widget-form').submit(function() {
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
           });
          return
        } else {
          $('#email-addresses-group').addClass('has-error');
          event.preventDefault();
        }

      });
    });
  </script>
  <!-- /Dashboard etc scripts -->
@append

