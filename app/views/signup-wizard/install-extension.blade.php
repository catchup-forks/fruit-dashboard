@extends('meta.base-user')

  @section('pageTitle')
    We have a Chrome extension
  @stop

  @section('pageStylesheet')
  @stop

  @section('pageContent')


  <div class="container">
    <div class="row not-visible margin-top">
      <div class="col-md-10 col-md-offset-1">
        <div class="panel panel-default panel-transparent">
          <div class="panel-body">
            <span class="pull-right" title="The extension substitutes your new tab with Fruit Dashboard." data-toggle="tooltip" data-placement="bottom">
              <sup>
                <i class="fa fa-2x fa-info-circle text-muted"></i>
              </sup>
            </span>
            <h1 class="text-center">
              Install Chrome extension
            </h1>
            
            <div class="row margin-top">
                <div class="col-md-4 col-md-offset-4">
                  <div class="panel panel-default">
                    <div class="panel-body text-center">
                      {{ HTML::image('img/logos/chrome.png', 'Chrome extension', array('class' => 'img-responsive img-rounded')) }}
                      
                      <p class="text-muted margin-top">
                        <span class="fa fa-chrome"> </span>
                        <small>Install the Chrome extension to access Fruit Dashboard on your new tab.</small>
                      </p>

                      <a href="https://chrome.google.com/webstore/detail/cgmdkfkbilmbclifhmfgabbkkcfjcicp" id="install-button" class="btn btn-primary btn-block margin-top connect-redirect" target="_blank" onclick="trackAll('lazy', {'en': 'clicked_on_install_extension', 'el': '{{ Auth::user()->email }}', });">Install Extension</a>
                      <p id="installed-p" class="text-success text-center lead margin-top">
                        <span class="fa fa-check"> </span> Extension installed
                      </p>
                      
                    </div> <!-- /.panel-body -->
                  </div> <!-- /.panel -->  
                </div> <!-- /.col-md-4 -->
            </div> <!-- /.row -->

            <hr>

            <div class="row">
              <div class="col-md-12">
                <a href="{{ route('signup-wizard.getStep', SiteConstants::getSignupWizardStep('prev', $currentStep)) }}" class="btn btn-warning">Back</a>
                <a href="{{ route('signup-wizard.getStep', SiteConstants::getSignupWizardStep('next', $currentStep)) }}" class="btn btn-primary pull-right">Finish</a>
                <a href="{{ route('signup-wizard.getStep', SiteConstants::getSignupWizardStep('next', $currentStep)) }}" class="btn btn-link pull-right">Skip and finish</a>
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
    if (window != window.top) {
      $('#install-button').hide();
      $('#installed-p').show();
    } else {
      $('#install-button').show();
      $('#installed-p').hide();
    }

    $(function(){
      setTimeout(function(){
        $('.not-visible').fadeIn();
      }, 1000);
    });

  </script>
  @stop
