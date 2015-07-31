<!-- if not on dashboard display the home button -->
@if (!Request::is('dashboard'))
	<div class="position-tl drop-shadow z-top">
	  <a href="/" alt="Dashboard" title="Dashboard">
	    <span class="fa fa-home fa-2x fa-inverse color-hovered"></span>
	  </a>
	</div>
@endif

<!-- add new widget button -->
<div class="position-bl drop-shadow z-top">
  <a href="{{ URL::route('widget.add') }}" alt="Add new widget" title="Add new widget">
    <span class="fa fa-plus-circle fa-2x fa-inverse color-hovered"></span>
  </a>
</div>


<!-- dropdown menu icon -->
<div class="btn-group position-tr z-top cursor-pointer">

	<span class="dropdown-icon fa fa-2x fa-cog fa-inverse color-hovered drop-shadow" alt="Settings" title="Settings" data-toggle="dropdown" aria-expanded="true"></span>

	<!-- dropdown menu elements -->
	<ul class="dropdown-menu pull-right" role="menu">
		<li>
			<a href="{{ URL::route('widget.add') }}">
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
			<a href="{{ URL::route('signup') }}">
				<span class="fa fa-cloud"></span> Sign up
			</a>
		</li>
		<li>
			<a href="{{ URL::route('auth.signin') }}">
				<span class="fa fa-sign-in"></span> Sign in
			</a>
		</li>
		@else
		<li>
			<a href="{{ URL::route('auth.signout') }}">
				<span class="fa fa-sign-out"></span> Sign out
			</a>
		</li>
		@endif
	</ul>

</div> <!-- /.btn-group -->

<!-- Display the Remaining Days counter -->
<a href="{{ route('payment.plans') }}" class="position-br drop-shadow z-top" data-toggle="tooltip" data-placement="left" title="Your trial period will end on <br> {{ Auth::user()->getTrialEndDate()->format('Y. m. d.') }} <br> Click to change your Plan.">
	<span class="label @if (Auth::user()->getDaysRemainingFromTrial() < 7) label-danger @else label-warning @endif label-as-badge valign-middle">{{ Auth::user()->getDaysRemainingFromTrial() }}</span>	
</a>

@section('pageScripts')

{{-- Initialize the tooltip for Remaining Days counter --}}
<script type="text/javascript">
	$(function () {
	  $('[data-toggle="tooltip"]').tooltip({
	  	html: true
	  })
	})
</script>

@append