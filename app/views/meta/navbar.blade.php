<div class="btn-group" id="settingsIcon">
	@if(!isset($onDashboard))<a class='link-button' id="homeButton" href="/" alt="Dashboard" title="Dashboard"><i class="fa fa-2x fa-home"></i></a>@endif
	<i class="dropdown-icon fa fa-2x fa-cog" id="rightDropDown"  alt="Settings" title="Settings" data-toggle="dropdown" aria-expanded="true"></i>
	<ul class="dropdown-menu pull-right" role="menu" aria-labelledby="rightDropDown">
		<li role="presentation">
			<a href="{{ URL::route('connect.connect') }}">
				<i class="dropdown-icon fa fa-plus-circle"></i>&nbsp;&nbsp;Add New Widget
			</a>
		</li>
		<li role="presentation">
			<a href="{{ URL::route('settings.settings') }}">
				<i class="dropdown-icon fa fa-cogs"></i>&nbsp;&nbsp;Settings
			</a>
		</li>
		<li role="presentation">
			<a href="https://fruitdashboard.uservoice.com/">
				<i class="dropdown-icon fa fa-bullhorn"></i>&nbsp;&nbsp;Feedback
			</a>
		</li>
		<li role="presentation">
			<a target="_blank" href="https://github.com/tryfruit/fruit-dashboard/">
				<i class="dropdown-icon fa fa-puzzle-piece"></i>&nbsp;&nbsp;Contribute
			</a>
		</li>
		@if (Auth::check() && Auth::user()->id==1)
		<li role="presentation">
			<a onClick= '_gaq.push(["_trackEvent", "Sign up", "Button Pushed"]);mixpanel.track("Signout");' href="{{ URL::route('auth.signup') }}">
				<i class="dropdown-icon fa fa-cloud"></i>&nbsp;&nbsp;Sign up
			</a>
		</li>
		<li role="presentation">
			<a onClick= '_gaq.push(["_trackEvent", "Sign in", "Button Pushed"]);mixpanel.track("Signout");' href="{{ URL::route('auth.signin') }}">
				<i class="dropdown-icon fa fa-sign-in"></i>&nbsp;&nbsp;Sign in
			</a>
		</li>
		@else
		<li role="presentation">
			<a onClick= '_gaq.push(["_trackEvent", "Sign out", "Button Pushed"]);mixpanel.track("Signout");' href="{{ URL::route('auth.signout') }}">
				<i class="dropdown-icon fa fa-sign-out"></i>&nbsp;&nbsp;Sign out
			</a>
		</li>
		@endif
	</ul>
</div>