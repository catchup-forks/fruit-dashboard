<!-- if not on dashboard display the home button -->
@if (!Request::is('dashboard'))
    <div class="position-tl drop-shadow z-top">
      <a href="{{ route('dashboard.dashboard') }}" alt="Dashboard" title="Dashboard">
        <span class="fa fa-fw fa-home fa-2x fa-inverse color-hovered"></span>
      </a>
    </div>
@endif

<!-- new shared widget notification -->
@if(Auth::user()->hasUnseenWidgetSharings())
    <div class="position-bl-second drop-shadow z-top">
      <a href="#" onclick="goToDashboard({{count($dashboards)-1}});" alt="New shared widget" title="You have a new shared widget on your dashboard. Click to see." data-toggle="tooltip" data-placement="right">
        <span class="fa fa-fw fa-share-square-o fa-2x fa-inverse color-hovered"></span>
      </a>
    </div>
@endif

<!-- add new widget button -->
<div class="position-bl drop-shadow z-top">
  <a href="{{ URL::route('widget.add') }}" alt="New widget" title="Add new widget" data-toggle="tooltip" data-placement="right">
    <span class="fa fa-fw fa-plus-circle fa-2x fa-inverse color-hovered"></span>
  </a>
</div>

@if (Request::is('dashboard'))
    <!-- dashboard lock icon -->
    <div id="dashboard-lock" class="position-br drop-shadow z-top" alt="Dashboard lock" title="" data-toggle="tooltip" data-placement="left" data-dashboard-id="" data-lock-direction="">
        <span class="fa fa-fw fa-unlock-alt fa-2x fa-inverse color-hovered"> </span>
    </div>
    <!-- /dashboard lock icon -->
@endif


<!-- dropdown menu icon -->
<div class="btn-group position-tr z-top cursor-pointer">

    <span class="dropdown-icon fa fa-fw fa-2x fa-cog fa-inverse color-hovered drop-shadow" alt="Settings" data-toggle="dropdown" aria-expanded="true"></span>

    <!-- dropdown menu elements -->
    <ul class="dropdown-menu pull-right" role="menu">
        <li>
            <a href="{{ URL::route('widget.add') }}">
                <span class="fa fa-plus-circle"></span> New Widget
            </a>
        </li>
        <li>
            <a href="{{ URL::route('settings.settings') }}">
                <span class="fa fa-cogs"></span> Settings
            </a>
        </li>
        <li>
            <a href="{{ URL::route('dashboard.manage') }}">
                <span class="fa fa-list"></span> Manage Dashboards
            </a>
        </li>
        @if (Request::is('dashboard'))
            <li>
                <a href="#" onclick="startTour();">
                    <span class="fa fa-question"></span> Take tour
                </a>
            </li>
        @endif
        <li>
            <a href="https://fruitdashboard.uservoice.com/" target="blank">
                <span class="fa fa-bullhorn"></span> Feedback
            </a>
        </li>
        <li>
            <a target="_blank" href="https://github.com/tryfruit/fruit-dashboard/" onclick="trackAll('lazy', {'en': 'Clicked on contribute plan', 'el': '{{ Auth::user()->email }}', });">
                <span class="fa fa-puzzle-piece"></span> Contribute
            </a>
        </li>
        <li>
            <a href="{{ URL::route('payment.plans') }}">
                <span class="fa fa-tag"></span> Plans
            </a>
        </li>
        <li>
            <a href="{{ URL::route('auth.signout') }}">
                <span class="fa fa-sign-out"></span> Sign out
            </a>
        </li>
    </ul>

</div> <!-- /.btn-group -->

<div class="position-tr-second z-top-under-dropdown cursor-pointer">

<a href="http://fruitdashboard.tryfruit.com/community/" class="fa fa-fw fa-2x fa-street-view fa-inverse color-hovered drop-shadow" alt="Community" data-toggle="tooltip" data-placement="left" title="Join our community" onclick="trackAll('lazy', {'en': 'clicked_on_community', 'el': '{{ Auth::user()->email }}', });" target="_blank"></a>
    
</div> <!-- /.position-tr-second -->


<!-- Display the Remaining Days counter -->
@if (Auth::user()->subscription->getSubscriptionInfo()['TD'])
<!--
    <a href="{{ route('payment.plans') }}"
       class="position-br drop-shadow z-top no-underline"
       data-toggle="tooltip"
       data-placement="left"
       title=
        "@if (Auth::user()->subscription->getSubscriptionInfo()['TS'] == 'active')
            Your trial period will end on <br> {{ Auth::user()->subscription->getSubscriptionInfo()['trialEndDate']->format('Y-m-d') }} <br> Click here to change your Plan.
        @else
            Your trial period ended on <br> {{ Auth::user()->subscription->getSubscriptionInfo()['trialEndDate']->format('Y-m-d') }} <br> Click here to change your Plan.
        @endif">

        <span class="label @if (Auth::user()->subscription->getSubscriptionInfo()['trialDaysRemaining'] < SiteConstants::getTrialPeriodInDays() / 2) label-danger @else label-warning @endif label-as-badge valign-middle">
            {{ Auth::user()->subscription->getSubscriptionInfo()['trialDaysRemaining'] }}
        </span>
    </a>
-->
@endif

@section('pageScripts')

{{-- Initialize the tooltip for Remaining Days counter --}}
<script type="text/javascript">
    $(function () {
      $('[data-toggle="tooltip"]').tooltip({
        html: true,
        container: 'body'
      });

      // Skips to the given dashboard (zero based index).
      goToDashboard = function(index){
          $('.carousel').carousel(index);
          $.ajax({
            type: 'get',
            url: '{{ route('widget.accept.all') }}' 
        })
      };
    })
</script>

@append
