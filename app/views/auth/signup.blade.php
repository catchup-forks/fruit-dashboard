@extends('meta.base-user-signout')

  @section('pageTitle')
    Sign up
  @stop

@section('pageStylesheet')

@stop

@section('pageContent')

<body @if(isset($isBackgroundOn)) @if($isBackgroundOn) style="background: url({{$dailyBackgroundURL}}) no-repeat center center fixed" @endif @endif>

<div class="vertical-center">
	<div class="container">

		<h1 class="text-white text-center drop-shadow">
		  Good <span class="greeting"></span>.
		</h1>  
		<h1 class="text-white text-center drop-shadow">
			What's your name?
		</h1>

		{{ Form::open(array('action' => 'AuthController@doSignup', 'id' => 'signup-form_id' )) }}
		{{ Form::text('name', Input::old('name'), array('autofocus' => true, 'autocomplete' => 'off', 'class' => 'form-control input-lg text-white drop-shadow text-center greetings-name', 'id' => 'username_id')) }}

				
	</div> <!-- /.container -->
</div> <!-- /.vertical-center -->

</body>
	
	@stop

	@section('pageScripts')

	<script type="text/javascript">
		$(document).ready(function() {

			var hours = new Date().getHours();
			
			if(17 <= hours || hours < 5) { $('.greeting').html('evening'); }
			if(5 <= hours && hours < 13) { $('.greeting').html('morning'); }
			if(13 <= hours && hours < 17) { $('.greeting').html('afternoon'); } 
		  
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

		  var hours = new Date().getHours();
		  
		  if(17 <= hours || hours < 5) { $('.greeting').html('evening'); }
		  if(5 <= hours && hours < 13) { $('.greeting').html('morning'); }
		  if(13 <= hours && hours < 17) { $('.greeting').html('afternoon'); } 
		  
		});
	</script>

@stop