@if(!isset($onDashboard))  
  <div class="position-tl drop-shadow z-top">
    <a href="/" alt="Dashboard" title="Dashboard">
      <span class="fa fa-home fa-2x fa-inverse color-hovered"></span>
    </a>  
  </div>
@endif

<div class="btn-group position-tr z-top cursor-pointer">
	
	<!-- dropdown menu icon -->
	<span class="dropdown-icon fa fa-2x fa-cog fa-inverse color-hovered drop-shadow" alt="Settings" title="Settings" data-toggle="dropdown" aria-expanded="true"></span>

	<!-- dropdown menu elements -->
	<ul class="dropdown-menu pull-right" role="menu">
		<li>
			<a href="{{ URL::route('connect.connect') }}">
				<span class="fa fa-plus-circle"></span> Add New Widget
			</a>
		</li>
		<li>
			<a href="{{ URL::route('settings.settings') }}">
				<span class="fa fa-cogs"></span> Settings
			</a>
		</li>
		<li>
			<a href="https://fruitdashboard.uservoice.com/">
				<span class="fa fa-bullhorn"></span> Feedback
			</a>
		</li>
		<li>
			<a target="_blank" href="https://github.com/tryfruit/fruit-dashboard/">
				<span class="fa fa-puzzle-piece"></span> Contribute
			</a>
		</li>
		@if (Auth::check() && Auth::user()->id==1)
		<li>
			<a onClick= '_gaq.push(["_trackEvent", "Sign up", "Button Pushed"]);mixpanel.track("Signout");' href="{{ URL::route('auth.signup') }}">
				<span class="fa fa-cloud"></span> Sign up
			</a>
		</li>
		<li>
			<a onClick= '_gaq.push(["_trackEvent", "Sign in", "Button Pushed"]);mixpanel.track("Signout");' href="{{ URL::route('auth.signin') }}">
				<span class="fa fa-sign-in"></span> Sign in
			</a>
		</li>
		@else
		<li>
			<a onClick= '_gaq.push(["_trackEvent", "Sign out", "Button Pushed"]);mixpanel.track("Signout");' href="{{ URL::route('auth.signout') }}">
				<span class="fa fa-sign-out"></span> Sign out
			</a>
		</li>
		@endif
	</ul>

</div> <!-- /.btn-group -->