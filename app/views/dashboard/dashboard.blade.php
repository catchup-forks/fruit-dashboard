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
          
          @if($dashboard->is_locked)
          <div class="lock-icon position-br-lg z-top fa-inverse color-hovered" data-toggle="tooltip" data-placement="left" title="This dashboard is locked. Click to unlock." data-dashboard-id="{{ $dashboard->id }}" data-lock-direction="unlock">
            <div class="drop-shadow">
              <span class="fa fa-lock"></span>
            </div>
          </div>
          @else
          <div class="lock-icon position-br-lg z-top fa-inverse color-hovered" data-toggle="tooltip" data-placement="left" title="This dashboard is unlocked. Click to lock." data-dashboard-id="{{ $dashboard->id }}" data-lock-direction="lock">
            <div class="drop-shadow">
              <span class="fa fa-unlock-alt"></span>
            </div>
          </div>
          @endif
          
          <div class="fill" @if(Auth::user()->background->is_enabled) style="background-image:url({{ Auth::user()->background->url }});" @endif>
          </div> <!-- /.fill -->

          {{-- Here comes the dashboard content --}}
          <div id="gridster-{{ $dashboard->id }}" class="gridster grid-base fill-height not-visible">

            {{-- Generate all the widgdets --}}
            <ul class="list-unstyled">

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

  <!-- Dashboard etc scripts -->
  <script type="text/javascript">
    // Initialize Carousel
    $('.carousel').carousel({
      interval: false // stops the auto-cycle
    })
  </script>
  <!-- /Dashboard etc scripts -->
@append

