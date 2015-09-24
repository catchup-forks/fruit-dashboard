@extends('meta.base-user')

  @section('pageTitle')
    Financial connections
  @stop

  @section('pageStylesheet')
  @stop

  @section('pageContent')


  <div class="container vertical-center">
    <div class="row not-visible">
      <div class="col-md-10 col-md-offset-1">
        <div class="panel panel-default panel-transparent">
          <div class="panel-body">
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
                      {{ HTML::image('img/logos/'.$service['name'].'.png', $service['name'], array('class' => 'img-responsive img-rounded')) }}

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
                <a href="{{ URL::route('signup-wizard.social-connections') }}" class="btn btn-primary pull-right">Next</a>
                <a href="{{ URL::route('signup-wizard.social-connections') }}" class="btn btn-link pull-right">Skip</a>
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
        e.preventDefault();
        bootbox.confirm({
          title: 'Fasten seatbelts, redirection ahead',
          message: 'To connect the service, we will redirect you to their site. Are you sure?',
          // On clicking OK redirect to fruit dashboard add widget page.
          callback: function(result) {
            if (result) {
              if (window!=window.top) {
                window.open(url, '_blank');
              } else {
                window.location = url;
              }
            }
          }
        });
      });

    })
  </script>
  

  @stop
