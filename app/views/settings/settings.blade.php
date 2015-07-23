@extends('meta.base-user')

  @section('pageTitle')
    Account settings
  @stop

  @section('pageStylesheet')
  @stop

  @section('pageContent')
  <div class="vertical-center">
    <div class="container">
      <div class="row">
        <div class="col-md-8 col-md-offset-2">
          <div class="panel panel-default panel-transparent margin-top">
            <div class="panel-heading">
              <h3 class="panel-title">
                <span class="fa fa-cog"></span>
                Account settings
              </h3>
            </div> <!-- /.panel-heading -->
            <div class="panel-body">


              {{-- Account settings - Username --}}
              {{-- START --}}
              {{ Form::open(array('route' => array('settings.change', 'name'), 'class' => 'form-horizontal' )) }}

                <div class="form-group">

                  {{ Form::label('name', 'Username', array(
                    'class' => 'col-sm-3 control-label' )) }}

                  <div class="col-sm-6">

                    {{ Form::text('name', $user->name, array(
                      'class' => 'form-control' )) }}

                  </div> <!-- /.col-sm-6 -->

                  <div class="col-sm-2">
                    
                    {{ Form::submit('Modify' , array(
                      'class' => 'btn btn-primary' )) }}

                  </div> <!-- /.col-sm-2 -->
                </div> <!-- /.form-group -->

              {{ Form::close() }}
              {{-- END --}}
              {{-- Account settings - Username  --}}

              {{-- Account settings - E-mail --}}
              {{-- START --}}
              {{ Form::open(array('route' => array('settings.change', 'email'), 'class' => 'form-horizontal' )) }}

                <div class="form-group">

                  {{ Form::label('email', 'E-mail', array(
                    'class' => 'col-sm-3 control-label' )) }}

                  <div class="col-sm-6">

                    {{ Form::text('email', $user->email, array(
                      'class' => 'form-control' )) }}

                  </div> <!-- /.col-sm-6 -->

                  <div class="col-sm-2">
                    
                    {{ Form::submit('Modify' , array(
                      'class' => 'btn btn-primary' )) }}

                  </div> <!-- /.col-sm-2 -->
                </div> <!-- /.form-group -->

              {{ Form::close() }}
              {{-- END --}}
              {{-- Account settings - E-mail  --}}

              {{-- Account settings - Password --}}
              {{-- START --}}
              {{--
              {{ Form::open(array('route' => array('settings.change', 'password'), 'class' => 'form-horizontal' )) }}

                <div class="form-group">

                  {{ Form::label('password', 'Password', array(
                    'class' => 'col-sm-3 control-label' )) }}

                  <div class="col-sm-6">

                    {{ Form::password('password', array(
                      'class' => 'form-control' )) }}

                  </div> <!-- /.col-sm-6 -->

                  <div class="col-sm-2">
                    
                    {{ Form::submit('Modify' , array(
                      'class' => 'btn btn-primary' )) }}

                  </div> <!-- /.col-sm-2 -->
                </div> <!-- /.form-group -->

              {{ Form::close() }}
              --}}
              {{-- END --}}
              {{-- Account settings - Password  --}}


            </div> <!-- /.panel-body -->
          </div> <!-- /.panel -->
        </div> <!-- /.col-md-6 -->
      </div> <!-- /.row -->
    </div> <!-- /.container -->
  </div> <!-- /.vertical-center -->

  {{-- 
    {{ $user }}
    {{ $settings }}
    {{ $subscription }}
   --}}

  @stop

  @section('pageScripts')
  @stop