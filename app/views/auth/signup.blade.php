@extends('meta.base-user-signout')

  @section('pageTitle')
    Sign up
  @stop

@section('pageStylesheet')
<!-- Facebook Conversion Code for Katt -->

@stop

@section('navbar')
@stop

@section('pageContent')

<body @if(isset($isBackgroundOn)) @if($isBackgroundOn) style="background: url({{$dailyBackgroundURL}}) no-repeat center center fixed" @endif @endif class="theme-asphalt page-signin" style="">
	<!-- Container -->
	<div class="signin-container">

		<!-- Left side -->
		<div class="signin-info">
			<a href="/" class="logo">
				Fruit Dashboard
			</a> <!-- / .logo -->
			<div class="slogan">
				The new tab for your startup.
			</div> <!-- / .slogan -->
			<!--ul>
				<li><i class="fa fa-sitemap signin-icon"></i> Attach your favourite </li>
				<li><i class="fa fa-file-text-o signin-icon"></i> Discuss it with your coworkers</li>
				<li><i class="fa fa-outdent signin-icon"></i> Your dashboard at your new tab</li>
				<li><i class="fa fa-heart signin-icon"></i> Designed with love</li>
			</ul--> <!-- / Info list -->
		</div>
		<!-- / Left side -->

		<!-- Right side -->
		<div class="signin-form">

			<!-- Form -->
			{{ Form::open(array('route' => 'auth.signup', 'id' => 'signup-form_id' )) }}
			<div class="signin-text">
				<span>Create an account</span>
			</div> <!-- / .signin-text -->

			<div class="form-group w-icon">
				{{ Form::text('email', Input::old('email'), array('autofocus' => true, 'placeholder' => 'Email@provider.com', 'class' => 'form-control input-lg signUp', 'id' => 'username_id')) }}
				<span class="fa fa-envelope signin-form-icon"></span>
			</div> <!-- / Username -->

			<div class="form-group w-icon">
				{{ Form::password('password', array('placeholder' => 'Password', 'class' => 'form-control input-lg', 'id' => 'password_id')) }}
				<span class="fa fa-lock signin-form-icon"></span>
			</div> <!-- / Password -->

			<div class="form-actions">
				{{ Form::submit('Sign up' , array(
					'id' => 'id_submit',
					'class' => 'signin-btn bg-primary',
					'onClick' => '_gaq.push(["_trackEvent", "Signup", "Button Pushed"]);mixpanel.track("Signup");')) }}
					<!-- <a href="#" class="forgot-password" id="forgot-password-link">Forgot your password?</a> -->
				</div> <!-- / .form-actions -->
				{{ Form::close() }}
				<!-- / Form -->

				<!-- Password reset form -->
				<div class="password-reset-form" id="password-reset-form">
					<div class="header">
						<div class="signin-text">
							<span>Password reset</span>
							<div class="close">×</div>
						</div> <!-- / .signin-text -->
					</div> <!-- / .header -->

					<!-- Form -->
					<form action="index.html" id="password-reset-form_id" novalidate="novalidate">
						<div class="form-group w-icon">
							<input type="text" name="password_reset_email" id="p_email_id" class="form-control input-lg" placeholder="Enter your email">
							<span class="fa fa-envelope signin-form-icon"></span>
						</div> <!-- / Email -->

						<div class="form-actions">
							<input type="submit" value="SEND PASSWORD RESET LINK" class="signin-btn bg-primary">
						</div> <!-- / .form-actions -->
					</form>
					<!-- / Form -->
				</div>
				<!-- / Password reset form -->
			</div>
			<!-- Right side -->
		</div>
		<!-- / Container -->
		<div class="not-a-member">
			Already have an account? <a href="{{ URL::route('auth.signin') }}">Sign in</a>.
		</div>
	
	@stop

	@section('pageScripts')

	<script type="text/javascript">
	// Resize BG
	init.push(function () {
		var $ph  = $('#page-signup-bg'),
		$img = $ph.find('> img');

		$(window).on('resize', function () {
			$img.attr('style', '');
			if ($img.height() < $ph.height()) {
				$img.css({
					height: '100%',
					width: 'auto'
				});
			}
		});	
	});

</script>

@stop