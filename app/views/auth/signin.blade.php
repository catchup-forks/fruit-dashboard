@extends('meta.base-user-signout')

  @section('pageTitle')
    Sign in
  @stop

@section('navbar')
@stop

@section('pageContent')

<body @if(isset($isBackgroundOn)) @if($isBackgroundOn) style="background: url({{$dailyBackgroundURL}}) no-repeat center center fixed" @endif @endif class="theme-asphalt page-signup" style="">

  <!-- Container -->
  <div class="signup-container">
    <!-- Header -->
    <div class="signup-header">
      <a href="/" class="logo">
        Fruit Dashboard
      </a> <!-- / .logo -->
      <div class="slogan">
        The new tab for your startup
      </div> <!-- / .slogan -->
    </div>
    <!-- Form -->
    <div class="signup-form">
      {{ Form::open(array('route' => 'auth.signin', 'id' => 'signin-form_id' )) }}        
        <div class="signup-text">
          <span>Sign In to your account</span>
        </div>

        <div class="form-group w-icon">
          {{ Form::text('email', Input::old('email'), array('autofocus' => true, 'placeholder' => 'Email@provider.com', 'class' => 'form-control input-lg', 'id' => 'email_id')) }}
          <span class="fa fa-envelope signup-form-icon"></span>
        </div>

        <div class="form-group w-icon">
          {{ Form::password('password', array('placeholder' => 'Password', 'class' => 'form-control input-lg', 'id' => 'password_id')) }}
          <span class="fa fa-lock signup-form-icon"></span>
        </div>

        <!-- <div class="form-group" style="margin-top: 20px;margin-bottom: 20px;">
          <label class="checkbox-inline">
            <input type="checkbox" name="signup_confirm" class="px" id="confirm_id">
            <span class="lbl">I agree with the <a href="#" target="_blank">Terms and Conditions</a></span>
          </label>
        </div> -->

        <div class="form-actions">
          {{ Form::submit('Sign in' , array(
            'id' => 'id_submit',
            'class' => 'signup-btn bg-primary',
            'onClick' => '_gaq.push(["_trackEvent", "Signup", "Button Pushed"]);mixpanel.track("Signup");' )) }}
        </div>
      {{ Form::close() }}
    </div>
    <!-- / Form -->
  </div>
  <!-- / Container -->

  <div class="have-account">
    Not a member yet? <a href="{{ URL::route('auth.signup') }}">Sign up</a>!
  </div>

</body>

@stop

@section('pageScripts')

<script type="text/javascript">
  // Resize BG
  $(document).ready(function() {
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

  // Show/Hide password reset form on click
  $(document).ready(function() {
    $('#forgot-password-link').click(function () {
      $('#password-reset-form').fadeIn(400);
      return false;
    });
    $('#password-reset-form .close').click(function () {
      $('#password-reset-form').fadeOut(400);
      return false;
    });
  });

</script>

@stop


