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
                <span class="fa fa-cog"></span>
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
                      'class' => 'btn btn-primary' )) }}

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
                      'class' => 'btn btn-primary' )) }}

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
          <div class="panel panel-default panel-transparent margin-top">
            <div class="panel-heading">
              <h3 class="panel-title">
                <span class="fa fa-cog"></span>
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
                      'class' => 'btn btn-primary' )) }}

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
          <div class="panel panel-default panel-transparent margin-top">
            <div class="panel-heading">
              <h3 class="panel-title">
                <span class="fa fa-cog"></span>
                Subscription settings
              </h3>
            </div> <!-- /.panel-heading -->
            <div class="panel-body">

              {{-- Subscription settings - Trial --}}
              <div class="row">
                <div class="col-md-6">
                  <div class="panel panel-default">
                    <div class="panel-heading">
                      <h3 class="panel-title">
                        <span class="fa fa-child"></span>
                        Your trial ends in
                      </h3>
                    </div> <!-- /.panel-heading -->
                    <div class="panel-body">
                      <h3>{{ Auth::user()->getDaysRemainingFromTrial() }} day(s)</h3>
                      <small>(on {{ Auth::user()->getTrialEndDate() }})</small>
                    </div> <!-- /.panel-body -->
                  </div> <!-- /.panel -->
                </div> <!-- /.col-md-6 -->
                <div class="col-md-6">
                  <div class="panel panel-default">
                    <div class="panel-heading">
                      <h3 class="panel-title">
                        <span class="fa fa-child"></span>
                        Plans and pricing
                      </h3>
                    </div> <!-- /.panel-heading -->
                    <div class="panel-body">
                      <a href="{{ route('payment.plans') }}">Change your plan</a>
                    </div> <!-- /.panel-body -->
                  </div> <!-- /.panel -->
                </div> <!-- /.col-md-6 -->
              </div> <!-- /.row -->
              {{-- Subscription settings - Trial --}}

            </div> <!-- /.panel-body -->
          </div> <!-- /.panel -->
        </div> <!-- /.col-md-6 -->
      </div> <!-- /.row -->
      {{-- /Subscription settings --}}

      {{-- Service connection settings --}}
      <div class="row">
        <div class="col-md-8 col-md-offset-2">
          <div class="panel panel-default panel-transparent margin-top">
            <div class="panel-heading">
              <h3 class="panel-title">
                <span class="fa fa-cog"></span>
                Service connectios
              </h3>
            </div> <!-- /.panel-heading -->
            <div class="panel-body">

              {{-- Service connection settings - Stripe --}}
              {{-- START --}}
              <div class="form-group">
                <div class="col-sm-8">
                  <h3>
                  <i class="fa fa-cc-stripe fa-2x"></i>
                    @if(Auth::user()->isStripeConnected()) 
                      <span class="label label-success">Connected</span>
                    @else
                      <span class="label label-danger">Not connected</span>
                    @endif
                  </h3>
                </div> <!-- /.col-sm-6 -->

                <div class="col-sm-2">
                  @if(Auth::user()->isStripeConnected()) 
                    <a class="btn btn-danger" href="{{ route('disconnect.stripe') }}">Disconnect</a>
                  @else
                    <a class="btn btn-success" href="{{ route('signup-wizard.financial-connections') }}">Connect</a>      
                  @endif
                </div> <!-- /.col-sm-2 -->
              </div> <!-- /.form-group -->

              <div class="form-group">
                <div class="col-sm-8">
                  <h3>
                  <i class="fa fa-cc-visa fa-2x"></i>
                    @if(Auth::user()->isBraintreeConnected()) 
                      <span class="label label-success">Connected</span>
                    @else
                      <span class="label label-danger">Not connected</span>
                    @endif
                  </h3>
                </div> <!-- /.col-sm-6 -->

                <div class="col-sm-2">
                  @if(Auth::user()->isStripeConnected()) 
                    <a class="btn btn-danger" href="{{ route('disconnect.stripe') }}">Disconnect</a>
                  @else
                    <a class="btn btn-success" href="{{ route('signup-wizard.financial-connections') }}">Connect</a>      
                  @endif
                </div> <!-- /.col-sm-2 -->
              </div> <!-- /.form-group -->

              {{-- END --}}
              {{-- Service connection settings - Background --}}

            </div> <!-- /.panel-body -->
          </div> <!-- /.panel -->
        </div> <!-- /.col-md-6 -->
      </div> <!-- /.row -->
      {{-- /Service connection settings --}}

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

        // Switch submit button to spinner
        form.find(':submit').hide();
        form.find(':submit').before($('<i/>', {'class': 'fa fa-spinner fa-spin fa-2x',}));
        
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

                  // Switch spinner to button
                  form.find('.fa-spinner').hide();
                  form.find(':submit').show();

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
                  
                  // Switch spinner to button
                  form.find('.fa-spinner').hide();
                  form.find(':submit').show();
               }
        });
      });
    </script>
  @stop