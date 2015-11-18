@extends('meta.base-user')

  @section('pageTitle')
    Financial connections
  @stop

  @section('pageStylesheet')
  @stop

  @section('pageContent')


  <div class="container">
    <div class="row not-visible margin-top">
      <div class="col-md-10 col-md-offset-1">
        <div class="panel panel-default panel-transparent">
          <div class="panel-body">
            <span class="pull-right" title="These provide data on your revenues." data-toggle="tooltip" data-placement="bottom">
              <sup>
                <i class="fa fa-2x fa-info-circle text-muted"></i>
              </sup>
            </span>
            <h1 class="text-center">
              Connect your financial accounts
            </h1>
            
            <div class="row margin-top">

              @foreach (SiteConstants::getServicesMetaByType('financial') as $index => $service)

                <div 
                @if( $index == 0)
                  class="col-md-4 col-md-offset-2" 
                @else 
                  class="col-md-4" 
                @endif>
                  <div class="panel panel-default">
                    <div class="panel-body text-center">
                      {{ HTML::image('img/logos/'.$service['name'].'.png', $service['display_name'], array('class' => 'img-responsive img-rounded')) }}

                        <span>{{ $service['display_name'] }}</span>

                      @if(Auth::user()->isServiceConnected($service['name']))
                        <p class="text-success text-center lead margin-top">
                          <span class="fa fa-check"> </span> Connected
                        </p>
                        
                      @else
                        <a href="{{ route($service['connect_route']) }}?createDashboard=1" class="btn btn-primary btn-block margin-top connect-redirect">Connect</a>

                      @endif

                      <p class="text-muted margin-top">
                        <span class="fa fa-lock"> </span>
                        <small>Your data is encrypted and held privately.</small>
                      </p>
                      
                    </div> <!-- /.panel-body -->
                  </div> <!-- /.panel -->  
                </div> <!-- /.col-md-4 -->

              @endforeach

            </div> <!-- /.row -->

            <hr>

            <div class="row">
              <div class="col-md-12">
                <a href="{{ route('signup-wizard.getStep', SiteConstants::getSignupWizardStep('prev', $currentStep)) }}" class="btn btn-warning">Back</a>
                <a href="{{ route('signup-wizard.getStep', SiteConstants::getSignupWizardStep('next', $currentStep)) }}" class="btn btn-primary pull-right">Next</a>
                <a href="{{ route('signup-wizard.getStep', SiteConstants::getSignupWizardStep('next', $currentStep)) }}" class="btn btn-link pull-right">Skip</a>
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

      // Service redirection
      $('.connect-redirect').click(function(e) {
        var url = $(this).attr('href');
        var service = $(this).prev().html();
            e.preventDefault();
            bootbox.dialog({
              title: 'We need you to allow Fruit Dashboard access.',
              message: 'To connect ' + service + ', we will redirect you to their site.',
              buttons: {
                cancel: {
                  label: 'Cancel',
                  className: 'btn-default',
                  callback: function(){}
                },
                main: {
                  label: 'Take me to ' + service,
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
