@extends('meta.base-user')

  @section('pageTitle')
    Financial connections
  @stop

  @section('pageStylesheet')
  @stop

  @section('pageContent')

  <div class="container">
    <h1 class="text-center text-white drop-shadow">
      Select your Startup type
    </h1> <!-- /.text-center -->

    <div class="row">
      <div class="col-md-10 col-md-offset-1">
        <div class="panel panel-default panel-transparent margin-top">
          <div class="panel-body">

            {{ Form::open(array(
              'route' => array('signup-wizard.select-startup-type'))) }}

            <div class="form-group">

              <div class="row">

                {{ Form::label('startup_type', 'Types', array(
                  'class' => 'col-sm-3 control-label'
                ))}}

                <div class="col-sm-6">

                  {{ Form::select('startup_type', SiteConstants::getStartupTypes(), null, array(
                      'class' => 'form-control'
                    ))}}

                </div> <!-- /.col-sm-6 -->

              </div> <!-- /.row -->

              <div class="row">

                <div class="col-md-12">

                  <hr>

                <div class="row">
                  <div class="col-md-12">
                  {{ Form::submit('Select', array(
                    'class' => 'btn btn-primary pull-right'
                  )) }}
                    <a href="{{ URL::route('signup-wizard.financial-connections') }}" class="btn btn-link pull-right">Skip</a>
                  </div> <!-- /.col-md-12 -->
                </div> <!-- /.row -->


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
