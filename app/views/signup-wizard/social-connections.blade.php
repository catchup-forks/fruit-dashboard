@extends('meta.base-user')

  @section('pageTitle')
    Financial connections
  @stop

  @section('pageStylesheet')
  @stop

  @section('pageContent')

  <div class="container">

    <h1 class="text-center text-white drop-shadow">
      Connect your social accounts.
    </h1>

    <div class="row">
      <div class="col-md-10 col-md-offset-1">
        <div class="panel panel-default panel-transparent">
          <div class="panel-body">
            <div class="row">

              <div class="col-md-5">

                <div class="list-group margin-top-sm">

                  <a href="
                    @if(Auth::user()->isStripeConnected())
                      {{ route('disconnect.stripe') }}
                    @else
                      {{ StripeConnector::getStripeConnectURI(URL::route('signup-wizard.financial-connections')); }}
                    @endif
                  " class="list-group-item clearfix changes-image" data-image="widget-stripe">
                    @if(Auth::user()->isFacebookConnected())
                        <small>
                          <span class="fa fa-circle text-success" data-toggle="tooltip" data-placement="left" title="Connection is alive."></span>
                        </small>
                    @else
                        <small>
                          <span class="fa fa-circle text-danger" data-toggle="tooltip" data-placement="left" title="Not connected"></span>
                        </small>
                    @endif
                    Stripe
                    <span class="pull-right">
                      @if(auth::user()->isstripeconnected())
                        <button class="btn btn-xs btn-danger">
                          Disconnect
                        </button>
                      @else
                        <button class="btn btn-xs btn-success" >
                          Connect
                        </button>
                      @endif
                    </span>
                  </a>

                  <a href="
                    @if(Auth::user()->isBraintreeConnected())
                      {{ route('disconnect.braintree') }}
                    @else
                      {{ route('signup-wizard.braintree-connect') }}
                    @endif
                  " class="list-group-item changes-image" data-image="widget-braintree">
                    @if(Auth::user()->isBraintreeConnected())
                      <small><span class="fa fa-circle text-success" data-toggle="tooltip" data-placement="left" title="Connection is alive."></span></small>
                    @else
                      <small><span class="fa fa-circle text-danger" data-toggle="tooltip" data-placement="left" title="Not connected"></span></small>
                    @endif
                    Braintree
                    <span class="pull-right">
                      @if(Auth::user()->isBraintreeConnected())
                        <button class="btn btn-xs btn-danger">
                          Disconnect
                        </button>
                      @else
                        <button class="btn btn-xs btn-success" >
                          Connect
                        </button>
                      @endif
                    </span>
                  </a>
                </div> <!-- /.list-group -->

              </div> <!-- /.col-md-5 -->
              <div class="col-md-7">
                {{ HTML::image('img/demonstration/widget-stripe.jpg', 'The Stripe Widget', array('id' => 'img-change', 'class' => 'img-responsive img-rounded')) }}
              </div> <!-- /.col-md-7 -->
            </div> <!-- /.row -->

            <hr>

            <div class="row">
              <div class="col-md-12">
                <a href="{{ URL::route('dashboard.dashboard') }}" class="btn btn-primary pull-right">Finish</a>

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
