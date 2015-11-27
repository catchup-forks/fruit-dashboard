@extends('meta.base-user')

  @section('pageTitle')
    Connect Fruit Dashboard to Slack
  @stop

  @section('pageStylesheet')
  @stop

  @section('pageContent')


  <div class="container">
    <div class="row not-visible margin-top">
      <div class="col-md-10 col-md-offset-1">
        <div class="panel panel-default panel-transparent">
          <div class="panel-body">
            <span class="pull-right" title="Connect Fruit Dashboard to one of your Slack channels, and get daily notifications about your progress. <hr / > All you need is to set up a Slack webhook and paste the url in the box below." data-toggle="tooltip" data-placement="bottom">
              <sup>
                <i class="fa fa-2x fa-info-circle text-muted"></i>
              </sup>
            </span>
            <h1 class="text-center">
              Connect to Slack
            </h1>
            
            <div class="row margin-top">
                <div class="col-md-4 col-md-offset-4">
                  <div class="panel panel-default">
                    <div class="panel-body text-center">
                      {{ HTML::image('img/logos/slack.png', 'Chrome extension', array('class' => 'img-responsive img-rounded')) }}
                      
                      <p class="text-muted margin-top">
                        <span class="fa fa-slack"> </span>
                        <small>Paste your Slack Webhook url in the field below.</small>
                      </p>

                    </div> <!-- /.panel-body -->
                  </div> <!-- /.panel -->  
                </div> <!-- /.col-md-4 -->
            </div> <!-- /.row -->
            
            <form method="POST" action="{{ route('signup-wizard.postStep', $currentStep) }}" class="form-horizontal">
              <div class="row margin-top">
                <div class="col-md-10 col-md-offset-1">
                  <div class="form-group">
                    <div class="col-sm-12">
                      <input name='address' type='text' class='form-control' placeholder='https://hooks.slack.com/services/<tokens>' value="{{ $address }}">
                    </div> <!-- /.col-sm-9 -->
                  </div> <!-- /.form-group -->
                </div> <!-- /.col-md-10 -->
              </div> <!-- /.row -->

              <hr>

              <div class="row">
                <div class="col-md-12">
                  <a href="{{ route('signup-wizard.getStep', SiteConstants::getSignupWizardStep('prev', $currentStep)) }}" class="btn btn-warning">Back</a>
                  <button type="submit" class='btn btn-primary pull-right'>Connect</button>      
                  <a href="{{ route('signup-wizard.getStep', SiteConstants::getSignupWizardStep('next', $currentStep)) }}" class="btn btn-link pull-right">Skip</a>
                </div> <!-- /.col-md-12 -->
              </div> <!-- /.row -->
            </form>

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
    });
  </script>
  @stop
