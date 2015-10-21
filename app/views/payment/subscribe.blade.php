@extends('meta.base-user')

  @section('pageTitle')
    Subscribe
  @stop

  @section('pageStylesheet')
  @stop

  @section('pageContent')
    <div class="vertical-center">
      <div class="container">

        <div class="row">
          <div class="col-md-6 col-md-offset-3">
            <div id="subscription-panel" class="panel panel-default panel-transparent not-visible">
              <div  class="panel-body text-center">
                {{ Form::open(array('route' => array('payment.subscribe', $plan->id))) }}
                  <div id="payment-form"></div>

                <div class="form-actions text-center">
                  <p>Subscribe to {{ $plan->name }} plan for <i class="fa fa-{{ $plan->braintree_merchant_currency }}"></i>{{ $plan->amount }}</p>
                  {{ Form::submit('Subscribe', array('class' => 'btn btn-success')) }}
                </div> <!-- / .form-actions -->
                
                {{ Form::close() }}
                </form>
              </div> <!-- /.panel-footer -->
              <div class="panel-footer text-center">
                We process your payment using Braintree.<br>
                You will be charged the same day every month. <br>
                You can cancel any time.
              </div> <!-- /.panel-footer -->
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

        $(document).ready(function(){
          $('#subscription-panel').fadeIn();
        });
    </script>
  @stop



