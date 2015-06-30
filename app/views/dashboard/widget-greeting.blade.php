<li class="dashboard-widget grey-hover no-padding" data-id='{{ $id }}'  data-row="{{ $position['row'] }}" data-col="{{ $position['col'] }}" data-sizex="{{ $position['x'] }}" data-sizey="{{ $position['y'] }}">
	<!--a class='link-button' href='' data-toggle="modal" data-target='#widget-settings-{{ $id }}'><span class="gs-option-widgets"></span></a-->
	<a href="{{ URL::route('connect.deletewidget', $id) }}">
    <span class="gs-close-widgets"></span>
  </a>
  <!-- If user is registered -->
  @if (Auth::user()->id != 1)
  	<p class='greetings-text white-text textShadow text-center'>
      Good <span class='greeting'></span>@if(isset(Auth::user()->name))<span class="greeting-comma">,</span> {{ Auth::user()->name }}@endif!
    </p>
  <!-- If user is not registered -->  
  @else 
    <p class='greetings-text white-text textShadow text-center'>
      
      <div class="yourname-form panel-padding">
      <span class="greetings-text-name white-text textShadow text-center">
        What's your name?
      </span>
      <!-- Form -->
        {{ Form::open(array('action' => 'AuthController@doSignup', 'id' => 'signup-form_id' )) }}
        {{ Form::text('name', Input::old('name'), array('autofocus' => true, 'autocomplete' => 'off', 'class' => 'form-control input-lg greetings-text white-text textShadow text-center userName', 'id' => 'username_id')) }}
      </div>

      <div class="youremail-form panel-padding hidden-form text-center">
        <span class="greetings-text-email white-text textShadow">
          Hey<span class="username"></span>, what is your email address?
        </span>
        <div class="form-group">
          {{ Form::text('email', Input::old('email'), array('autocomplete' => 'off', 'autocorrect' => 'off', 'class' => 'form-control input-lg greetings-text white-text textShadow text-center userName', 'id' => 'email_id')) }}
        </div>
      </div> <!-- / Username -->

      <div class="yourpassword-form panel-padding hidden-form text-center">
        <span class="greetings-text-password white-text textShadow">
          …and you'll need a password.
        </span>
        <div class="form-group">
          {{ Form::password('password', array('class' => 'form-control input-lg greetings-text white-text textShadow text-center userName', 'id' => 'password_id')) }}
        </div>
      </div> <!-- / Password -->

      <div class="form-actions hidden-form">
        {{ Form::submit('Sign up' , array(
          'id' => 'id_submit',
          'class' => 'btn btn-success btn-flat',
          'onClick' => '_gaq.push(["_trackEvent", "Signup", "Button Pushed"]);mixpanel.track("Signup");')) }}
          <span class="white-text">or <a class="link-white" href="signin">Login here</a></span>
        </div> <!-- / .form-actions -->
        {{ Form::close() }}
      </div>

    </p>
  @endif
</li>
