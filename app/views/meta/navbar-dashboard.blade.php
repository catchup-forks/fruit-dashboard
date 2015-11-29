<!-- dropdown menu icon -->
<div class="btn-group position-tr z-top cursor-pointer">

    <span class="dropdown-icon fa fa-fw fa-2x fa-cog fa-inverse color-hovered drop-shadow" alt="Settings" data-toggle="dropdown" aria-expanded="true"></span>

    <!-- dropdown menu elements -->
    <ul class="dropdown-menu pull-right" role="menu">
        <li>
            <a href="{{ route('widget.add') }}">
                <span class="fa fa-plus-circle"></span> New Widget
            </a>
        </li>
        <li>
            <a href="{{ route('settings.settings') }}">
                <span class="fa fa-cogs"></span> Settings
            </a>
        </li>
        <li>
            <a href="{{ route('dashboard.manage') }}">
                <span class="fa fa-list"></span> Manage Dashboards
            </a>
        </li>
        <li>
            <a href="#" onclick="startTour();">
                <span class="fa fa-question"></span> Take tour
            </a>
        </li>
        <li>
            <a href="{{ route('notification.configureSlack') }}">
                <span class="fa fa-slack"></span> Slack Integration
            </a>
        </li>
        <li>
            <a href="http://fruitdashboard.tryfruit.com/community/" onclick="trackAll('lazy', {'en': 'clicked_on_community', 'el': '{{ Auth::user()->email }}', });" target="_blank">
                <span class="fa fa-street-view"></span> Join the Community
            </a>
        </li>
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
            <a href="{{ route('payment.plans') }}">
                <span class="fa fa-tag"></span> Plans
            </a>
        </li>
        <li>
            <a href="{{ route('auth.signout') }}">
                <span class="fa fa-sign-out"></span> Sign out
            </a>
        </li>
    </ul>

</div> <!-- /.btn-group -->

@section('pageScripts')
{{-- Initialize the tooltip for Remaining Days counter --}}
<script type="text/javascript">
    $(function () {
      $('[data-toggle="tooltip"]').tooltip({
        html: true,
        container: 'body'
      });

    })
</script>
@append
