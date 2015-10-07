@extends('meta.base-user')

  @section('pageTitle')
    Financial connections
  @stop

  @section('pageStylesheet')
  @stop

  @section('pageContent')

  <div class="container">
    <h1 class="text-center text-white drop-shadow">
      Select your Google Analytics profiles
    </h1> <!-- /.text-center -->

    <div class="row">
      <div class="col-md-10 col-md-offset-1">
        <div class="panel panel-default panel-transparent margin-top">
          <div class="panel-body">

            {{ Form::open(array(
              'route' => array('service.google_analytics.select-properties'))) }}

            <div class="form-group">

              <div class="row">

                {{ Form::label('properties', 'Google Analytics profile', array(
                  'class' => 'col-sm-3 control-label'
                ))}}

                <div class="col-sm-6">

                  {{ Form::select('profiles[]', $profiles, null, array(
                      'class' => 'form-control'
                    ))}}

                </div> <!-- /.col-sm-6 -->

              </div> <!-- /.row -->

              <div class="row">

                <div class="col-md-12">

                  <hr>

                  <a href="{{ route('signup-wizard.social-connections') }}" class="btn btn-warning">Cancel</a>

                  {{ Form::submit('Select', array(
                    'class' => 'btn btn-primary pull-right'
                  )) }}

                </div> <!-- /.col-md-12 -->

              </div> <!-- /.row -->

            </div> <!-- /.form-group -->

            {{ Form::close() }}

          </div> <!-- /.panel-body -->
        </div> <!-- /.panel -->
      </div> <!-- /.col-md-10 -->
    </div> <!-- /.row -->
  </div> <!-- /.container -->

  @stop

  @section('pageScripts')
  @stop
