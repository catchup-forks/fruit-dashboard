@extends('meta.base-user')

  @section('pageTitle')
    Select profile
  @stop

  @section('pageStylesheet')
  @stop

  @section('pageContent')

  <div class="container">
    <h1 class="text-center text-white drop-shadow">
      Select your Google Analytics profile
    </h1> <!-- /.text-center -->

    <div class="row">
      <div class="col-md-10 col-md-offset-1">
        <div class="panel panel-default panel-transparent margin-top">
          <div class="panel-body">
           
            @if (count($profiles) > 0)
              <form method="POST" action="{{ route('signup-wizard.postStep', $currentStep) }}" class="form-horizontal">

              <div class="form-group">
                <div class="row">
                  <div class="col-sm-6 col-sm-offset-3">
                    {{ Form::select('profiles', $profiles, null, array(
                        'class' => 'form-control', 
                        'size'  => 15, 
                        'id'    => 'profile-select'))
                    }}
                  </div>
                </div> <!-- /.row -->
              </div> <!-- /.form-group -->

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
                <div class="col-md-4 col-md-offset-4" >
                  <div class="panel panel-default">
                    <div class="panel-body text-center">
                      <p class="text-success text-center lead margin-top">
                        Sorry, you don't have any google analytics profile.
                      </p>
                      <p class="text-muted margin-top">
                        <span class="fa fa-check"> </span>
                        <small>Please proceed with the next step.</small>
                      </p>
                    </div> <!-- /.panel-body -->
                  </div> <!-- /.panel -->  
                </div> <!-- /.col-md-4 -->
              </div> <!-- /.row -->

              <div class="row">
                <div class="col-md-12">
                  <a href="{{ route('signup-wizard.getStep', SiteConstants::getSignupWizardStep('prev', $currentStep)) }}" class="btn btn-warning">Back</a>
                  <button type="submit" class='btn btn-primary pull-right'>Next</button>
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
@append
