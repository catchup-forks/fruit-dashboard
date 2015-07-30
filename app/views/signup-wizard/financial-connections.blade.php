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

                <div class="row">
                  <div class="col-sm-7">
                    <a href="{{ StripeConnector::getStripeConnectURI(URL::route('signup-wizard.financial-connections')); }}" class="stripe-connect no-underline changes-image" data-image="widget-stripe"><span>Connect with Stripe</span></a>
                  </div> <!-- /.col-sm-7 -->
                  <div class="col-sm-5">
                    @if(Auth::user()->isStripeConnected())
                      <p class="text-success pull-right">
                        <span class="fa fa-circle" data-toggle="tooltip" data-placement="left" title="Connection is alive."></span>
                      </p>
                    @else
                      <p class="text-danger pull-right">
                        <span class="fa fa-circle" data-toggle="tooltip" data-placement="left" title="Not connected"></span>
                      </p>
                    @endif
                  </div> <!-- /.col-sm-5 -->
                </div> <!-- /.row -->

                <div class="row margin-top-sm">
                  <div class="col-sm-7">
                    <a href="{{ route('signup-wizard.braintree-connect') }}" class="changes-image" data-image="widget-braintree">
                      {{ HTML::image('img/third-party/braintree-button.png', 'Connect with Braintree', array('height' => '35')) }}
                    </a>
                  </div> <!-- /.col-sm-7 -->
                  <div class="col-sm-5">
                    @if(Auth::user()->isBraintreeConnected())
                      <p class="text-success pull-right">
                        <span class="fa fa-circle" data-toggle="tooltip" data-placement="left" title="Connection is alive."></span>
                      </p>
                    @else
                      <p class="text-danger pull-right">
                        <span class="fa fa-circle" data-toggle="tooltip" data-placement="left" title="Not connected"></span>
                      </p>
                    @endif
                  </div> <!-- /.col-sm-5 -->
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
                
                <a href="{{ URL::route('dashboard.dashboard') }}">
                  Skip this step.
                </a>    
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

  {{-- Initialize tooltips --}}
  <script type="text/javascript">
    $(function () {
      $('[data-toggle="tooltip"]').tooltip()
    })
  </script>
  @stop
