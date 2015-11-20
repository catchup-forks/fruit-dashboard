@extends('meta.base-user-signout')

  @section('pageTitle')
    Reset password
  @stop

@section('pageContent')

<body style="background: url({{ Background::getBaseUrl() }}) no-repeat center center fixed">

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
              Send password reminder
            </h3>
          </div> <!-- /.panel-heading -->
          <div class="panel-body">

            <!-- Form -->
            {{ Form::open(array('route' => 'auth.doRemind')) }}

            <form autocomplete="off">
              {{ Form::text('email', Input::old('email'), array('autofocus' => true, 'placeholder' => 'email@provider.com', 'class' => 'form-control form-group input-lg col-sm-12', 'id' => 'email_id')) }}


              {{ Form::submit('Submit' , array(
                'id' => 'id_submit',
                'class' => 'btn btn-primary pull-right')) }}

              {{ Form::close() }}

            </form>
            <!-- /Form -->

            <a href="{{ route('auth.signin') }}">
              <button class="btn btn-primary">
                 Cancel
              </button>
            </a>

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


