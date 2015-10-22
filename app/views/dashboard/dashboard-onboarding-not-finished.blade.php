@extends('meta.base-user')

  @section('pageTitle')
    Onboarding not finished
  @stop

  @section('pageStylesheet')
  @stop

  @section('pageContent')


  <div class="container">
    <div class="row not-visible margin-top">
      <div class="col-md-10 col-md-offset-1">
        <div class="panel panel-default panel-transparent">
          <div class="panel-body">
            <h1 class="text-center">
              You didn't finish your onboarding wizard
            </h1> <!-- /.text-center -->
            
            <div class="row margin-top">
              <div class="col-md-6 col-md-offset-3" >
                <div class="panel panel-default">
                  <div class="panel-body text-center">
                    <p>
                    {{ HTML::image('/img/icon128x128.png', "Fruit Dashboard", array('class' => 'img-responsive img-rounded center-block')) }}
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

  <script type="text/javascript">
    $(function(){

      setTimeout(function(){
        $('.not-visible').fadeIn();
      }, 1000);

    })    
  </script>
  

  @stop
