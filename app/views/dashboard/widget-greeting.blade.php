<li class="dashboard-widget grey-hover no-padding" data-id='{{ $id }}'  data-row="{{ $position['row'] }}" data-col="{{ $position['col'] }}" data-sizex="{{ $position['x'] }}" data-sizey="{{ $position['y'] }}">
	<!--a class='link-button' href='' data-toggle="modal" data-target='#widget-settings-{{ $id }}'><span class="gs-option-widgets"></span></a-->
	<a href="{{ URL::route('connect.deletewidget', $id) }}">
    <span class="gs-close-widgets"></span>
  </a>
  <!-- If user is registered -->
  @if (Auth::user()->id != 1)
  	<p class='greetings-text white-text textShadow text-center'>Good <span class='greeting'></span>
      @if(isset(Auth::user()->name))
      <span class="greeting-comma">,</span><input id="userName" value="{{ Auth::user()->name }}" class="form-control white-text textShadow text-center userName" name="userName" type="text">@endif!
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

@section('pageModals')
	<!-- greetings settings -->
	
	@include('settings.widget-settings')

	<!-- /greetings settings -->
@append
<script type="text/javascript">
// if user is registered, saveUserName function
@if (Auth::user()->id != 1)
  init.push(function () {
    
    if ($('#userName').val().length === 0){
      $('.greeting-comma').addClass('hidden-form');
    }

    function saveUserName(event) {
      if ($('#userName').val().length === 0){
        $('.greeting-comma').addClass('hidden-form');
      }
      else {
        $('.greeting-comma').removeClass('hidden-form');
      }
      var keycode = (event.keyCode ? event.keyCode : event.which);
      if(keycode == '13'){
        event.preventDefault();  
      }          
      var newName = $(event.target).val();
      if (newName) {
        $.ajax({
          type: 'POST',
          url: '/widgets/settings/username/' + newName,
          success:function(message,code){
          }
        });            
      }
    }
    $('#userName').keyup(_.debounce(saveUserName,1000));
  });
// if user is not registered, signup form
@else 
  init.push(function () {
    
    $('#username_id').on('keydown', function (event){
      var keycode = (event.keyCode ? event.keyCode : event.which);
      if(keycode == '13' || keycode == '9'){
        event.preventDefault();
        $('.yourname-form').slideUp('fast', function (){
          $('.youremail-form').find('span.username').html(' ' + $('#username_id').val());
          $('.youremail-form').slideDown('fast', function() {
            $('#email_id').focus();
          });
        });
      }    
    });

    $('#email_id').on('keydown', function (event){
      var keycode = (event.keyCode ? event.keyCode : event.which);
      if(keycode == '13' || keycode == '9'){
        event.preventDefault();
        $('.youremail-form').slideUp('fast', function (){
          $('.yourpassword-form').slideDown('fast', function() {
            $('#password_id').focus();
          });
        });
      }    
    });

    $('#password_id').on('keydown', function (event){
      
    });
    
  });
@endif
</script> 