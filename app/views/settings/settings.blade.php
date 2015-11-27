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
                  'data-setting-name' => 'background-enabled',
                  'class' => 'form-horizontal settings-form' )) }}

                <div class="form-group">

                  {{ Form::label('background', 'Background enabled', array(
                    'class' => 'col-sm-3 control-label' )) }}

                  <div class="col-sm-6">

                    {{ Form::select('background-enabled',
                       array('1' => 'Yes', '0' => 'No'),
                       Auth::user()->background->is_enabled,
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
              {{-- General settings - Background enabled --}}

              {{-- General settings - Background change --}}
              {{-- START --}}
              {{ Form::open(array(
                  'data-setting-name' => 'background-change',
                  'class' => 'form-horizontal settings-form' )) }}

                <div class="form-group">
                  <label for="changeBackground" class="col-sm-3 control-label">
                    Background picture
                  </label>
                  <div class="col-sm-6">
                    <p class="form-control-static">
                      Change the current background picture
                      {{ Form::hidden('background-change'); }}
                    </p>
                  </div> <!-- /.col-sm-6 -->
                  <div class="col-sm-2">
                    {{ Form::submit('Modify' , array(
                      'class' => 'btn btn-primary',
                      'data-loading-text' => 'Saving...' )) }}
                  </div> <!-- /.col-sm-2 -->
                </div> <!-- /.form-group -->

              {{ Form::close() }}
              {{-- END --}}
              {{-- General settings - Background change --}}

            </div> <!-- /.panel-body -->
          </div> <!-- /.panel -->
        </div> <!-- /.col-md-6 -->
      </div> <!-- /.row -->
      {{-- /General settings --}}

      {{-- Slack Integration --}}
      <div class="row">
        <div class="col-md-8 col-md-offset-2">
          <div class="panel panel-default panel-transparent">
            <div class="panel-heading">
              <h3 class="panel-title">
                <span class="fa fa-slack"></span>
                Slack Integration
              </h3>
            </div> <!-- /.panel-heading -->
            <div class="panel-body">

              <div class="row">
                <div class="col-md-12">
                  <form class="form-horizontal">
                    <div class="form-group">
                      <label for="subscription" class="col-sm-3 control-label">
                        Integration status
                      </label>
                      <div class="col-sm-6">
                        <p class="form-control-static">
                          @if (Auth::user()->notifications()->where('type','slack')->first()->is_enabled)
                            Enabled
                          @else
                            Not enabled
                          @endif
                        </p>
                      </div> <!-- /.col-sm-6 -->
                      <div class="col-sm-2">
                        <a href="{{ route('notification.configureSlack') }}" class="btn btn-primary">Configure</a>
                      </div> <!-- /.col-sm-2 -->
                    </div> <!-- /.form-group -->
                  </form>
                </div> <!-- /.col-md-12 -->
              </div> <!-- /.row -->
              
            </div> <!-- /.panel-body -->
          </div> <!-- /.panel -->
        </div> <!-- /.col-md-6 -->
      </div> <!-- /.row -->
      {{-- /Subscription settings --}}

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
                        <a href="{{ route('payment.plans') }}" class="btn btn-primary">Modify</a>
                      </div> <!-- /.col-sm-2 -->
                    </div> <!-- /.form-group -->
                  </form>
                </div> <!-- /.col-md-12 -->
              </div> <!-- /.row -->
              <div class="row">
                <div class="col-md-12 text-center">
                  @if (Auth::user()->subscription->getSubscriptionInfo()['TD'])
                    @if (Auth::user()->subscription->getSubscriptionInfo()['TS'] == 'active')
                      <p>
                        Your trial ends in
                        <strong>
                          {{ Auth::user()->subscription->getSubscriptionInfo()['trialDaysRemaining'] }} day(s)
                        </strong>
                        <small class="text-muted">on {{ Auth::user()->subscription->getSubscriptionInfo()['trialEndDate']->format('Y-m-d')  }}.</small>
                      </p>
                    @else
                      <p>
                        Your trial has ended on {{ Auth::user()->subscription->getSubscriptionInfo()['trialEndDate']->format('Y-m-d')  }}. Change your plan to use the premium features.
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
                @foreach (SiteConstants::getAllServicesMeta() as $service)
                    @if(Auth::user()->isServiceConnected($service['name']))
                      <a href="{{ route($service['disconnect_route']) }}" class="list-group-item clearfix changes-image" data-image="widget-{{$service['name']}}">
                    @else
                      <a href="{{ route($service['connect_route']) }}" class="list-group-item clearfix changes-image connect-redirect" data-image="widget-{{$service['name']}}">
                    @endif

                    @if(Auth::user()->isServiceConnected($service['name']))
                        <small>
                          <span class="fa fa-circle text-success" data-toggle="tooltip" data-placement="left" title="Connection is alive."></span>
                        </small>
                    @else
                        <small>
                          <span class="fa fa-circle text-danger" data-toggle="tooltip" data-placement="left" title="Not connected"></span>
                        </small>
                    @endif

                    <span class="service-name">{{ $service['display_name'] }}</span>
                    <span class="pull-right">
                    @if(Auth::user()->isServiceConnected($service['name']))
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
                @endforeach
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
      // Service redirection
      $('.connect-redirect').click(function(e) {
        var url = $(this).attr('href');
        var service = $(this).find('.service-name').html();
        e.preventDefault();
        bootbox.dialog({
          title: 'We need you to allow Fruit Dashboard access.',
          message: 'To connect ' + service + ', we will redirect you to their site.',
          buttons: {
            cancel: {
              label: 'Cancel',
              className: 'btn-default',
              callback: function(){}
            },
            main: {
              label: 'Take me to ' + service,
              className: 'btn-primary',
              callback: function(result) {
                if (result) {
                  if (window!=window.top) {
                    window.open(url, '_blank');
                  } else {
                    window.location = url;
                  }
                }
              }
            }  
          }
        });
      });

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
                    easyGrowl('success', data.success, 3000);
                  } else if (data.error) {
                    easyGrowl('error', data.error, 3000);
                  };

                  // Reset button
                  form.find(':submit').button('reset');

                  // Reload page on certain changes
                  if (setting.indexOf('background') > -1) {
                    location.reload();
                  };

               },
               error: function(){
                  easyGrowl('error', "Something went wrong, we couldn't edit your settings. Please try again.", 3000);
                  // Reset button
                  form.find(':submit').button('reset');
               }
        });
      });
    </script>
  @stop