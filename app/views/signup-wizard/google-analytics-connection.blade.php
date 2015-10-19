@extends('meta.base-user')

  @section('pageTitle')
    Google Analytics connection
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
              Connect your Google Analytics account
            </h1>
            
            <div class="row margin-top">
              <div class="col-md-4 col-md-offset-4" >
                <div class="panel panel-default">
                  <div class="panel-body text-center">
                    {{ HTML::image('img/logos/analytics.png', $service['display_name'], array('class' => 'img-responsive img-rounded')) }}

                    <small>Assess the efficiency of the acquisition, activation and retention processes.</small>

                    @if(Auth::user()->isServiceConnected($service['name']))
                      <p class="text-success text-center lead margin-top">
                        <span class="fa fa-check"> </span> Connected
                      </p>
                    @else
                      <a href="{{ route($service['connect_route']) }}?signupWizard=1" class="btn btn-primary btn-block margin-top connect-redirect">Connect</a>
                    @endif
                    <p class="text-muted margin-top">
                      <span class="fa fa-lock"> </span>
                      <small>Your data is encrypted and held privately.</small>
                    </p>
                  </div> <!-- /.panel-body -->
                </div> <!-- /.panel -->  
              </div> <!-- /.col-md-4 -->
            </div> <!-- /.row -->

            <hr>

            @if(Auth::user()->isServiceConnected($service['name']))
              <div class="row">
                <div class="col-md-12">
                  <a href="{{ route('signup-wizard.getStep', SiteConstants::getSignupWizardStep('prev', $currentStep)) }}" class="btn btn-warning">Back</a>
                  <a href="{{ route('signup-wizard.getStep', SiteConstants::getSignupWizardStep('next', $currentStep)) }}" class="btn btn-primary pull-right">Next</a>
                  <a href="{{ route('signup-wizard.getStep', SiteConstants::getSignupWizardStep('next', $currentStep)) }}" class="btn btn-link pull-right">Skip</a>
                </div> <!-- /.col-md-12 -->
              </div> <!-- /.row -->
            @else
              <div class="row">
                <div class="col-md-12">
                  <a href="{{ route('signup-wizard.getStep', SiteConstants::getSignupWizardStep('prev', $currentStep)) }}" class="btn btn-warning">Back</a>
                  <a href="{{ route('signup-wizard.getStep', SiteConstants::getSignupWizardStep('next', $currentStep, true)) }}" class="btn btn-primary pull-right">Next</a>
                  <a href="{{ route('signup-wizard.getStep', SiteConstants::getSignupWizardStep('next', $currentStep, true)) }}" class="btn btn-link pull-right">Skip</a>
                </div> <!-- /.col-md-12 -->
              </div> <!-- /.row -->
            @endif
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

      // Service redirection
      $('.connect-redirect').click(function(e) {
        var url = $(this).attr('href');
        e.preventDefault();
        bootbox.dialog({
          title: 'We need you to allow Fruit Dashboard access.',
          message: 'To connect {{ $service["display_name"] }}, we will redirect you to their site.',
          buttons: {
            cancel: {
              label: 'Cancel',
              className: 'btn-default',
              callback: function(){}
            },
            main: {
              label: 'Take me to {{ $service["display_name"] }}',
              className: 'btn-primary',
              callback: function(result) {
                if (result) {
                  if (window!=window.top) {
                    window.open(url, '_blank');
                  } else {
                    window.location = url;
                  }
                }
              }
            }  
          }
        });
      });

    })
  </script>
  

  @stop
