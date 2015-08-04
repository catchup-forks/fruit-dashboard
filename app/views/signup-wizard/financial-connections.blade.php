@extends('meta.base-user')

  @section('pageTitle')
    Financial connections
  @stop

  @section('pageStylesheet')
  @stop

  @section('pageContent')

  <div class="container">
    
    <h1 class="text-center text-white drop-shadow">
      Connect your financial tools
    </h1>

    <div class="row">
      <div class="col-md-10 col-md-offset-1">
        <div class="panel panel-default panel-transparent">
          <div class="panel-body">
            <div class="row">

              <div class="col-md-5">

                <div class="row changes-image" data-image="widget-stripe">
                  <div class="col-md-8">
                    <h3 class="no-margin">
                      @if(Auth::user()->isStripeConnected())
                          <small><span class="fa fa-circle text-success" data-toggle="tooltip" data-placement="left" title="Connection is alive."></span></small>
                      @else
                          <small><span class="fa fa-circle text-danger" data-toggle="tooltip" data-placement="left" title="Not connected"></span></small>
                      @endif
                      <span class="label label-default">Stripe</span>
                    </h3>
                  </div> <!-- /.col-md-8 -->
                  <div class="col-md-4">
                    @if(Auth::user()->isStripeConnected())
                      <a class="btn btn-sm btn-danger pull-right" href="{{ route('disconnect.stripe') }}">Disconnect</a>
                    @else
                      <a class="btn btn-sm btn-success pull-right" href="{{ StripeConnector::getStripeConnectURI(URL::route('signup-wizard.financial-connections')); }}">Connect</a>
                    @endif
                  </div> <!-- /.col-md-4 -->
                </div> <!-- /.row -->

                <div class="row margin-top-sm changes-image" data-image="widget-braintree">
                  <div class="col-md-8">
                      <h3 class="no-margin">
                        @if(Auth::user()->isBraintreeConnected())
                          <small><span class="fa fa-circle text-success" data-toggle="tooltip" data-placement="left" title="Connection is alive."></span></small>
                        @else
                          <small><span class="fa fa-circle text-danger" data-toggle="tooltip" data-placement="left" title="Not connected"></span></small>
                        @endif
                        <span class="label label-default">Braintree</span>
                      </h3>
                  </div> <!-- /.col-md-8 -->
                  <div class="col-md-4">
                    @if(Auth::user()->isBraintreeConnected())
                      <a class="btn btn-sm btn-danger pull-right" href="{{ route('disconnect.braintree') }}">Disconnect</a>
                    @else
                      <a class="btn btn-sm btn-success pull-right" href="{{ route('signup-wizard.braintree-connect') }}">Connect</a>
                    @endif
                  </div> <!-- /.col-md-4 -->
                </div> <!-- /.row -->
                
              </div> <!-- /.col-md-5 -->
              <div class="col-md-7">
                {{ HTML::image('img/demonstration/widget-stripe.jpg', 'The Stripe Widget', array('id' => 'img-change', 'class' => 'img-responsive img-rounded')) }}
              </div> <!-- /.col-md-7 -->
            </div> <!-- /.row -->

            <hr>

            <div class="row">
              <div class="col-md-12">
                <a href="{{ URL::route('dashboard.dashboard') }}" class="btn btn-primary pull-right">Take me to my dashboard</a>
                 
              </div> <!-- /.col-md-12 -->
            </div> <!-- /.row -->

            

          </div> <!-- /.panel-body -->
        </div> <!-- /.panel -->
      </div> <!-- /.col-md-10 -->
    </div> <!-- /.row -->
  </div> <!-- /.container -->

  @stop

  @section('pageScripts')

  {{-- Change image on hover --}}
  <script type="text/javascript">
    $(function(){
      var baseUrl = "/img/demonstration/";
      var ext = ".jpg";

      $('.changes-image').hover(
        //on mouse enter
        function() {
          //rewrite img src and change alternate text
          $('#img-change').attr('src', baseUrl + $(this).data('image') + ext);
          $('#img-change').attr('alt', "The " + $(this).data('image') + " Widget.");
        });
    });
  </script>
  
  @stop
