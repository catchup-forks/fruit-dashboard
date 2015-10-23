@extends('meta.base-user')

  @section('pageTitle')
    Select profile
  @stop

  @section('pageStylesheet')
  @stop

  @section('pageContent')

  <div class="container">
    <div class="row not-visible margin-top">
      <div class="col-md-10 col-md-offset-1">
        <div class="panel panel-default panel-transparent">
          <div class="panel-body">
            <span class="pull-right" title="Select the most important profile now. You will be able to add more later on. <hr / > If you have no profiles set up yet, use Google Analytics to add your first one." data-toggle="tooltip" data-placement="bottom">
              <sup>
                <i class="fa fa-2x fa-info-circle text-muted"></i>
              </sup>
            </span>
            
            <h1 class="text-center">
              Select a Google Analytics profile to analyze
            </h1> <!-- /.text-center -->

            <div class="row margin-top">
              
              <div class="col-md-12">
                @if (count($profiles) > 0)
                  <form method="POST" action="{{ route('signup-wizard.postStep', $currentStep) }}" class="form-horizontal">

                  <div class="form-group">
                    <div class="row">
                      <div class="col-sm-6 col-sm-offset-3">
                        {{ Form::select('profiles', $profiles, null, array(
                            'class' => 'form-control', 
                            'id'    => 'profile-select'))
                        }}
                      </div>
                    </div> <!-- /.row -->
                  </div> <!-- /.form-group -->

                  <hr />

                  <div class="row">
                    <div class="col-md-12">
                      <a href="{{ route('signup-wizard.getStep', SiteConstants::getSignupWizardStep('prev', $currentStep)) }}" class="btn btn-warning">Back</a>
                      <button type="submit" class='btn btn-primary pull-right'>Next</button>
                      <a href="{{ route('signup-wizard.getStep', SiteConstants::getSignupWizardStep('next', $currentStep, true)) }}" class="btn btn-link pull-right">Skip</a>
                    </div> <!-- /.col-md-12 -->
                  </div> <!-- /.row -->

                  </form>

                @else
                  <div class="row margin-top">
                    <div class="col-md-10 col-md-offset-1" >
                      <div class="panel panel-default">
                        <div class="panel-body text-center">
                          <p class="lead margin-top">
                            You don't have any google analytics profiles set up yet.
                          </p>
                          <p class="text-muted margin-top">
                            <small>Set up a profile to have superb analytics.</small>
                          </p>
                        </div> <!-- /.panel-body -->
                      </div> <!-- /.panel -->  
                    </div> <!-- /.col-md-10 -->
                  </div> <!-- /.row -->

                  <hr />

                  <div class="row">
                    <div class="col-md-12">
                      <a href="{{ route('signup-wizard.getStep', SiteConstants::getSignupWizardStep('prev', $currentStep)) }}" class="btn btn-warning">Back</a>
                      <button type="submit" class='btn btn-primary pull-right'>Next</button>
                      <a href="{{ route('signup-wizard.getStep', SiteConstants::getSignupWizardStep('next', $currentStep, true)) }}" class="btn btn-link pull-right">Skip</a>
                    </div> <!-- /.col-md-12 -->
                  </div> <!-- /.row -->
                @endif  
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
