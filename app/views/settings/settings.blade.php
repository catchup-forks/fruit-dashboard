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
                <div class="col-md-6">
                  <div class="panel panel-default">
                    <div class="panel-heading">
                      <h3 class="panel-title">
                        <span class="fa fa-calendar-times-o"></span>
                        Your trial ends in
                      </h3>
                    </div> <!-- /.panel-heading -->
                    <div class="panel-body text-center">
                      <h3>{{ Auth::user()->getDaysRemainingFromTrial() }} day(s)</h3>
                      <small class="text-muted">on {{ Auth::user()->getTrialEndDate() }}</small>
                    </div> <!-- /.panel-body -->
                  </div> <!-- /.panel -->
                </div> <!-- /.col-md-6 -->
                <div class="col-md-6">
                  <div class="panel panel-default">
                    <div class="panel-heading">
                      <h3 class="panel-title">
                        <span class="fa fa-tags"></span>
                        Plans and pricing
                      </h3>
                    </div> <!-- /.panel-heading -->
                    <div class="panel-body">
                      <a href="{{ route('payment.plans') }}" class="btn btn-block btn-success">Change your plan</a>
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
              <div class="row">
                
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
                    <a class="btn btn-sm btn-success pull-right" href="{{ route('signup-wizard.financial-connections') }}">Connect</a>
                  @endif
                </div> <!-- /.col-md-4 -->
              
              </div> <!-- /.row -->

              <div class="row margin-top-sm">
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
                    <a class="btn btn-sm btn-success pull-right" href="{{ route('signup-wizard.financial-connections') }}">Connect</a>
                  @endif
                </div> <!-- /.col-md-4 -->
              </div> <!-- /.row -->

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

    {{-- Initialize tooltips --}}
    <script type="text/javascript">
      $(function () {
        $('[data-toggle="tooltip"]').tooltip({
          html: true;
        })
      })
    </script>
  @stop