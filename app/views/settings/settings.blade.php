@extends('meta.base-user')

  @section('pageTitle')
    Account settings
  @stop

  @section('pageStylesheet')
  @stop

  @section('pageContent')
    <div class="container">
      {{-- Account settings --}}
      <div class="row">
        <div class="col-md-8 col-md-offset-2">
          <div class="panel panel-default panel-transparent margin-top">
            <div class="panel-heading">
              <h3 class="panel-title">
                <span class="fa fa-book"></span>
                Account settings
              </h3>
            </div> <!-- /.panel-heading -->
            <div class="panel-body">


              {{-- Account settings - Username --}}
              {{-- START --}}
              {{ Form::open(array(
                  'data-setting-name' => 'name',
                  'class' => 'form-horizontal settings-form' )) }}

                <div class="form-group">

                  {{ Form::label('name', 'Username', array(
                    'class' => 'col-sm-3 control-label' )) }}

                  <div class="col-sm-6">

                    {{ Form::text('name', Auth::user()->name, array(
                      'class' => 'form-control' )) }}

                  </div> <!-- /.col-sm-6 -->

                  <div class="col-sm-2">

                    {{ Form::submit('Modify' , array(
                      'class' => 'btn btn-primary',
                      'data-loading-text' => 'Saving...' )) }}

                  </div> <!-- /.col-sm-2 -->
                </div> <!-- /.form-group -->

              {{ Form::close() }}
              {{-- END --}}
              {{-- Account settings - Username  --}}

              {{-- Account settings - E-mail --}}
              {{-- START --}}
              {{ Form::open(array(
                  'data-setting-name' => 'email',
                  'class' => 'form-horizontal settings-form' )) }}

                <div class="form-group">

                  {{ Form::label('email', 'E-mail', array(
                    'class' => 'col-sm-3 control-label' )) }}

                  <div class="col-sm-6">

                    {{ Form::text('email', Auth::user()->email, array(
                      'class' => 'form-control' )) }}

                  </div> <!-- /.col-sm-6 -->

                  <div class="col-sm-2">

                    {{ Form::submit('Modify' , array(
                      'class' => 'btn btn-primary',
                      'data-loading-text' => 'Saving...' )) }}

                  </div> <!-- /.col-sm-2 -->
                </div> <!-- /.form-group -->

              {{ Form::close() }}
              {{-- END --}}
              {{-- Account settings - E-mail  --}}

            </div> <!-- /.panel-body -->
          </div> <!-- /.panel -->
        </div> <!-- /.col-md-6 -->
      </div> <!-- /.row -->
      {{-- /Account settings --}}

      {{-- General settings --}}
      <div class="row">
        <div class="col-md-8 col-md-offset-2">
          <div class="panel panel-default panel-transparent">
            <div class="panel-heading">
              <h3 class="panel-title">
                <span class="fa fa-sliders"></span>
                General settings
              </h3>
            </div> <!-- /.panel-heading -->
            <div class="panel-body">

              {{-- General settings - Background --}}
              {{-- START --}}
              {{ Form::open(array(
                  'data-setting-name' => 'background',
                  'class' => 'form-horizontal settings-form' )) }}

                <div class="form-group">

                  {{ Form::label('background', 'Background enabled', array(
                    'class' => 'col-sm-3 control-label' )) }}

                  <div class="col-sm-6">

                    {{ Form::select('background',
                       array('1' => 'Yes', '0' => 'No'),
                       Auth::user()->settings->background_enabled,
                       array('class' => 'form-control' )); }}

                  </div> <!-- /.col-sm-6 -->

                  <div class="col-sm-2">

                    {{ Form::submit('Modify' , array(
                      'class' => 'btn btn-primary',
                      'data-loading-text' => 'Saving...' )) }}

                  </div> <!-- /.col-sm-2 -->
                </div> <!-- /.form-group -->

              {{ Form::close() }}
              {{-- END --}}
              {{-- General settings - Background --}}

            </div> <!-- /.panel-body -->
          </div> <!-- /.panel -->
        </div> <!-- /.col-md-6 -->
      </div> <!-- /.row -->
      {{-- /General settings --}}

      {{-- Subscription settings --}}
      <div class="row">
        <div class="col-md-8 col-md-offset-2">
          <div class="panel panel-default panel-transparent">
            <div class="panel-heading">
              <h3 class="panel-title">
                <span class="fa fa-credit-card"></span>
                Subscription settings
              </h3>
            </div> <!-- /.panel-heading -->
            <div class="panel-body">

              {{-- Subscription settings - Trial --}}
              <div class="row">
                <div class="col-md-12">
                  <form class="form-horizontal">
                    <div class="form-group">
                      <label for="subscription" class="col-sm-3 control-label">
                        Current plan
                      </label>
                      <div class="col-sm-6">
                        <p class="form-control-static">
                          {{ Auth::user()->subscription->plan->name }}
                        </p>
                      </div> <!-- /.col-sm-6 -->
                      <div class="col-sm-2">
                        <a href="{{ route('payment.plans') }}" class="btn btn-primary">Change</a>
                      </div> <!-- /.col-sm-2 -->
                    </div> <!-- /.form-group -->
                  </form>
                </div> <!-- /.col-md-12 -->
              </div> <!-- /.row -->
              <div class="row">
                <div class="col-md-12 text-center">
                  @if (Auth::user()->subscription->getTrialInfo()['enabled'])
                    @if (Auth::user()->subscription->getTrialInfo()['daysRemaining'] > 0)
                      <p>
                        Your trial ends in
                        <strong>
                          {{ Auth::user()->subscription->getTrialInfo()['daysRemaining'] }} day(s)
                        </strong>
                        <small class="text-muted">on {{ Auth::user()->subscription->getTrialInfo()['endDate']->format('Y-m-d')  }}.</small>
                      </p>
                    @else
                      <p>
                        Your trial has ended on {{ Auth::user()->subscription->getTrialInfo()['endDate']->format('Y-m-d')  }}. Change your plan to use the premium features.
                      </p>
                    @endif
                  @endif
                </div> <!-- /.col-md-12 -->
              </div> <!-- /.row -->
              {{-- Subscription settings - Trial --}}

            </div> <!-- /.panel-body -->
          </div> <!-- /.panel -->
        </div> <!-- /.col-md-6 -->
      </div> <!-- /.row -->
      {{-- /Subscription settings --}}

      {{-- Manage connection settings --}}
      <div class="row">
        <div class="col-md-8 col-md-offset-2">
          <div class="panel panel-default panel-transparent">
            <div class="panel-heading">
              <h3 class="panel-title">
                <span class="fa fa-wrench"></span>
                Manage connections
              </h3>
            </div> <!-- /.panel-heading -->
            <div class="panel-body">

              {{-- Manage connection settings --}}
              {{-- START --}}
              <div class="list-group margin-top-sm">

                  <a href="
                    @if(Auth::user()->isStripeConnected())
                      {{ route('service.stripe.disconnect') }}
                    @else
                      {{ StripeConnector::getStripeConnectURI(URL::route('signup-wizard.financial-connections')); }}
                    @endif
                  " class="list-group-item clearfix changes-image" data-image="widget-stripe">
                    @if(Auth::user()->isStripeConnected())
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
                      @if(Auth::user()->isStripeConnected())
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
                      {{ route('service.braintree.disconnect') }}
                    @else
                      {{ route('service.braintree.connect') }}
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

                  <a href="
                    @if(Auth::user()->isTwitterConnected())
                      {{ route('service.twitter.disconnect') }}
                    @else
                      {{ route('service.twitter.connect') }}
                    @endif
                  " class="list-group-item changes-image" data-image="widget-twitter">
                    @if(Auth::user()->isTwitterConnected())
                      <small><span class="fa fa-circle text-success" data-toggle="tooltip" data-placement="left" title="Connection is alive."></span></small>
                    @else
                      <small><span class="fa fa-circle text-danger" data-toggle="tooltip" data-placement="left" title="Not connected"></span></small>
                    @endif
                    Twitter
                    <span class="pull-right">
                      @if(Auth::user()->isTwitterConnected())
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

              {{-- END --}}
              {{-- Manage connection settings - Background --}}

            </div> <!-- /.panel-body -->
          </div> <!-- /.panel -->
        </div> <!-- /.col-md-6 -->
      </div> <!-- /.row -->
      {{-- /Manage connection settings --}}

    </div> <!-- /.container -->

   {{--
    {{ $user }}
    {{ $settings }}
    {{ $subscription }}
    --}}

  @stop

  @section('pageScripts')
    <script type="text/javascript">
      $(".settings-form").submit(function(e) {
        e.preventDefault();

        // initialize url
        var form    = $(this);
        var setting = $(this).attr("data-setting-name");
        var url     = "{{ route('settings.change', 'setting-name') }}".replace('setting-name', setting)

        // Change button text while loading
        form.find(':submit').button('loading');

        // Call ajax function
        $.ajax({
          type: "POST",
          dataType: 'json',
          url: url,
               data: form.serialize(),
               success: function(data) {
                  if (data.success) {
                    $.growl.notice({
                      title: "Success!",
                      message: data.success,
                      size: "large",
                      duration: 3000,
                      location: "br"
                    });
                  } else if (data.error) {
                    $.growl.error({
                      title: "Error!",
                      message: data.error,
                      size: "large",
                      duration: 3000,
                      location: "br"
                    });
                  };

                  // Reset button
                  form.find(':submit').button('reset');

                  // Reload page on certain changes
                  if (setting == 'background') {
                    location.reload();
                  };

               },
               error: function(){
                  $.growl.error({
                    title: "Error!",
                    message: "Something went wrong, we couldn't edit your settings. Please try again.",
                    size: "large",
                    duration: 3000,
                    location: "br"
                  });

                  // Reset button
                  form.find(':submit').button('reset');
               }
        });
      });
    </script>
  @stop