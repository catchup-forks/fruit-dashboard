@extends('meta.base-user')

  @section('pageTitle')
    Braintree payment
  @stop

  @section('pageStylesheet')
  @stop

  @section('pageContent')
    <div class="vertical-center">
      <div class="container">

        <div class="row">
          <div class="col-md-6 col-md-offset-3">
            <div class="panel panel-default panel-transparent">
              <div class="panel-body text-center">
                <form id="checkout" method="post" action="{{ route('payment.subscribe', $plan->id) }}">
                  <div id="payment-form"></div>
                  <input class="btn btn-success btn-block" type="submit" value="Subscribe to {{ $plan->name }} plan for €{{ $plan->amount }}">
                </form>
              </div> <!-- /.panel-body -->
            </div> <!-- /.panel -->
          </div> <!-- /.col-md-6 -->
        </div> <!-- /.row -->

      </div> <!-- /.container -->
    </div> <!-- /.vertical-center -->
  @stop

  @section('pageScripts')
    <script src="https://js.braintreegateway.com/v2/braintree.js"></script>
    <script>
        // Generated client token
        var clientToken = "{{ Braintree_ClientToken::generate() }}";

        // Initialize payment form
        braintree.setup(clientToken, "dropin", {
          container: "payment-form"
        });
    </script>
  @stop



