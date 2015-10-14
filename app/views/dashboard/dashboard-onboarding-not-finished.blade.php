@extends('meta.base-user')

  @section('pageTitle')
    Onboarding not finished
  @stop

  @section('pageStylesheet')
  @stop

  @section('pageContent')


  <div class="container">
    <div class="row">
      <div class="col-md-10 col-md-offset-1">
        <div class="panel panel-default panel-transparent margin-top">
          <div class="panel-body">
            
            <div class="row margin-top">
              <div class="col-md-6 col-md-offset-3" >
                <div class="panel panel-default">
                  <div class="panel-body text-center">
                    <p>
                    {{ HTML::image('/img/icon128x128.png', "Fruit Dashboard", array('class' => 'img-responsive img-rounded center-block')) }}
                    </p>
                    <p class="text-success text-center lead margin-top">
                      You didn't finish your onboarding wizard
                    </p>
                    <p class="text-muted margin-top">
                      <span class="fa fa-check"> </span>
                      <small>You can still use Fruit Dashboard, but probably some functions will be disabled.</small>
                    </p>
                  </div> <!-- /.panel-body -->
                </div> <!-- /.panel -->  
              </div> <!-- /.col-md-6 -->
            </div> <!-- /.row -->

            <hr>

            <div class="row">
              <div class="col-md-12">
                <a href="{{ route('signup-wizard.getStep', $currentState) }}" class="btn btn-primary pull-right">Take me to the onboarding wizard</a>
                <a href="{{ route('signup-wizard.getStep', 'finished') }}" class="btn btn-link pull-right">I'm fine, take me to my dashboard</a>
              </div> <!-- /.col-md-12 -->
            </div> <!-- /.row -->

          </div> <!-- /.panel-body -->
        </div> <!-- /.panel -->
      </div> <!-- /.col-md-10 -->
    </div> <!-- /.row -->
  </div> <!-- /.container -->

  @stop

  @section('pageScripts')
  @stop
