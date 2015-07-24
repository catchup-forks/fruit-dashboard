@extends('meta.base-user-signout')

  @section('pageTitle')
    Sign in
  @stop

@section('pageContent')

<body style="background: url({{ Background::dailyBackgroundURL() }}) no-repeat center center fixed">

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
              Sign in to your account
            </h3>
          </div> <!-- /.panel-heading -->
          <div class="panel-body">
            
            <!-- Form -->
            {{ Form::open(array('route' => 'auth.signin', 'id' => 'signin-form_id' )) }}

            <form autocomplete="off">
              <!-- Chrome only fills the first two input fields. -->
              <!-- By adding these two hidden ones Chrome autofill can be hidden. -->
              <input style="display:none">
              <input type="password" style="display:none">
              <!-- End of Chrome autofill hack. -->

              {{ Form::text('email', Input::old('email'), array('autofocus' => true, 'placeholder' => 'email@provider.com', 'class' => 'form-control form-group input-lg col-sm-12', 'id' => 'email_id')) }}

              {{ Form::password('password', array('placeholder' => 'password', 'class' => 'form-control form-group input-lg col-sm-12', 'id' => 'password_id')) }}

              {{ Form::submit('Sign in' , array(
                'id' => 'id_submit',
                'class' => 'btn btn-primary pull-right')) }}

              {{ Form::close() }}

            </form>

            <!-- /Form -->

          </div> <!-- /.panel-body -->
        </div> <!-- /.panel -->
      </div> <!-- /.col-md-6 -->
    </div> <!-- /.row -->

    <div class="row">
      <div class="col-md-6 col-md-offset-3">
        <div class="panel panel-default panel-transparent">
          <div class="panel-body text-center">
            Not a member yet? <a href="{{ URL::route('signup') }}">Sign up</a>!
          </div> <!-- /.panel-body -->
        </div> <!-- /.panel -->
      </div> <!-- /.col-md-6 -->
    </div> <!-- /.row -->

  </div> <!-- /.container -->
</div> <!-- /.vertical-center -->


</body>

@stop

@section('pageScripts')

@stop


