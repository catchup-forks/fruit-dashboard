@extends('meta.base-user-signout')

  @section('pageTitle')
    Sign up
  @stop

@section('pageStylesheet')

@stop

@section('navbar')
@stop

@section('pageContent')

<body @if(isset($isBackgroundOn)) @if($isBackgroundOn) style="background: url({{$dailyBackgroundURL}}) no-repeat center center fixed" @endif @endif>

<div class="vertical-center">
	<div class="container">
		<div class="row">
			<div class="col-md-6 col-md-offset-3">
				<div class="panel panel-default panel-transparent">
				  <div class="panel-body">
				  	<h1 class="text-center">
				  		Fruit Dashboard
				  	</h1>
				  	<p class="lead text-center">
				  		The new tab for your startup.
				  	</p>	
				  </div> <!-- /.panel-body -->
				</div> <!-- /.panel -->
			</div> <!-- /.col-md-6 -->
		</div> <!-- /.row -->

		<div class="row">
			<div class="col-md-6 col-md-offset-3">
				<div class="panel panel-default panel-transparent">
					<div class="panel-heading">
						<h3 class="panel-title text-center">
							Create account
						</h3>
					</div> <!-- /.panel-heading -->
				  <div class="panel-body">
				  	
				  	<!-- Form -->
				  	{{ Form::open(array('route' => 'auth.signup', 'id' => 'signup-form_id' )) }}

				  	<form autocomplete="off">
				  		<!-- Chrome only fills the first two input fields. -->
				  		<!-- By adding these two hidden ones Chrome autofill can be hidden. -->
				  		<input style="display:none">
				  		<input type="password" style="display:none">
				  		<!-- End of Chrome autofill hack. -->

				  		{{ Form::text('email', Input::old('email'), array('autofocus' => true, 'placeholder' => 'email@provider.com', 'class' => 'form-control form-group input-lg col-sm-12', 'id' => 'username_id')) }}
				  		{{ Form::password('password', array('placeholder' => 'choose a password', 'class' => 'form-control form-group input-lg col-sm-12', 'id' => 'password_id')) }}				  	

				  		{{ Form::submit('Sign up' , array(
				  			'id' => 'id_submit',
				  			'class' => 'btn btn-primary pull-right',
				  			'onClick' => '_gaq.push(["_trackEvent", "Signup", "Button Pushed"]);mixpanel.track("Signup");')) }}

				  	</form>

			  		{{ Form::close() }}
			  		<!-- / Form -->	

				  </div> <!-- /.panel-body -->
				</div> <!-- /.panel -->
			</div> <!-- /.col-md-6 -->
		</div> <!-- /.row -->


		<div class="row">
			<div class="col-md-6 col-md-offset-3">
				<div class="panel panel-default panel-transparent">
				  <div class="panel-body text-center">
				  	Already have an account? <a href="{{ URL::route('auth.signin') }}">Sign in</a>.
				  </div> <!-- /.panel-body -->
				</div> <!-- /.panel -->
			</div> <!-- /.col-md-6 -->
		</div> <!-- /.row -->
				
	</div> <!-- /.container -->
</div> <!-- /.vertical-center -->
	
	@stop

	@section('pageScripts')

@stop