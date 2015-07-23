@extends('meta.base-user')

  @section('pageTitle')
    Account settings
  @stop

  @section('pageStylesheet')
  @stop

  @section('pageContent')
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
                  
                  {{ Form::submit('Change name' , array(
                    'class' => 'btn btn-primary' )) }}

                </div> <!-- /.col-sm-2 -->
              </div> <!-- /.form-group -->

            {{ Form::close() }}
            {{-- END --}}
            {{-- Account settings - Username  --}}

          </div> <!-- /.panel-body -->
        </div> <!-- /.panel -->
      </div> <!-- /.col-md-6 -->
    </div> <!-- /.row -->
  </div> <!-- /.container -->

  {{ $user }}
  {{ $settings }}
  {{ $subscription }}

  @stop

  @section('pageScripts')
  @stop