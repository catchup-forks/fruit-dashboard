@extends('meta.base-user')

@section('pageContent')

<div class='page-pricing'>
  @parent
  <div id="plansContainer" class="container">
    <h3 class="containerHeader">Plans</h3>
    <div class="row">

      <!-- Trial Plan -->
      <div id="freePlan" class='plan-col col-xs-3 col-xs-offset-3'>
        <div class='plan-header bg-light-green darker'>
          <h4>Free</h4>
          <span>{{$plans[0]->description}}</span>
        </div>
        <div class='plan-pricing bg-light-green darken'>
          <span class='plan-currency'>{{ Config::get('constants')[strtolower($plans[0]->currencyIsoCode)] }}</span>
          <span class='plan-value'>0</span>
          <span class='plan-period'>/MO</span>
        </div>
        <ul class='plan-features'>
          <li>Create graphs</li>
          <li>Up to 3 service connections</li>
          <li>Premium support</li>
          <a href='#' class='bg-light-green darker' data-plan="trial"><h4>START TRIAL</h4></a>
        </ul>
      </div>
      <!-- /Trial Plan -->

      <!-- Premium Plan -->
      <div id="premiumPlan" class='plan-col col-xs-3'>
        <div class='plan-header bg-light-green darker'>
          <h4>Premium</h4>
          <span>{{$plans[0]->description}}</span>
        </div>
        <div class='plan-pricing bg-light-green darken'>
          <span class='plan-currency'>{{ Config::get('constants')[strtolower($plans[0]->currencyIsoCode)] }}</span>
          <span class='plan-value'>{{ round($plans[0]->price) }}</span>
          <span class='plan-period'>/MO</span>
        </div>
        <ul class='plan-features'>
          <li>Create graphs</li>
          <li>Up to 3 service connections</li>
          <li>Premium support</li>
          <a class='bg-light-green darker' data-toggle="modal" data-target="#modal-payment" href="#" data-plan="apple_pack"><h4>SIGN UP</h4></a>
        </ul>
      </div>
      <!-- /Premium Plan -->

    </div>      <!-- /.row -->
    <h4 class="containerHeader">Github</h4>
  </div>      <!-- /#plansContainer -->
  @stop
</div>      <!-- /.page-pricing -->

<!-- Modals -->
<div id="modal-payment" class="modal fade" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <form id="checkout" method="post" action="{{ URL::route ('payment.planname', array('apple_pack')) }}">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h4 class="modal-title">Subscribe!</h4>
      </div>
      <div class="modal-body">
        <div id="dropin"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        <input class='btn btn-info' value="Subscribe" type="submit">
      </div>
    </form>
    </div> <!-- / .modal-content -->
  </div> <!-- / .modal-dialog -->
</div>

<div id="modal-trial" class="modal fade" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <form id="checkout" method="post" action="">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h4 class="modal-title">Subscribe!</h4>
      </div>
      <div class="modal-body">
      yep its free for 30 days
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        <a class='btn btn-info' value="Subscribe" href="plan/trial">Subscribe</a>
      </div>
    </form>
    </div> <!-- / .modal-content -->
  </div> <!-- / .modal-dialog -->
</div>
<!-- /Modals -->

@section('pageScripts')
<!-- Braintree JS library -->
{{ HTML::script('https://js.braintreegateway.com/v2/braintree.js') }}

<script type="text/javascript">
init.push(function(){
  $('#modal-payment').on('show.bs.modal', function (event) {
    braintree.setup(
      '{{ $clientToken }}',
      'dropin', {
        container: 'dropin'
      }
    );
  });
});


</script>
@stop