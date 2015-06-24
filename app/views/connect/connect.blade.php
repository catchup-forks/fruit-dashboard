@extends('meta.base-user')

  @section('pageTitle')
    Connect
  @stop

  @section('pageContent')
    <div id="content-wrapper">
        <div class="col-md-10 col-md-offset-1">
            <div class="col-md-2 col-sm-3 col-xs-4 no-padding">
              <div class="settingsWidget white-background">
              <a href="{{ URL::route('connect.addwidget', 'clock') }}">
              <div class="settingsWidgetContent">
                <span class="icon fa fa-clock-o fa-3x"></span>
                <p>Clock</p>
              </div>          <!-- content -->
              </a>
              </div>
            </div>          <!-- col-xs-2 -->
            <div class="col-md-2 col-sm-3 col-xs-4 no-padding">
              <div class="settingsWidget white-background">
              <a href="{{ URL::route('connect/new', ['provider' => 'text', 'step' => 'init']) }}">
              <div class="settingsWidgetContent">
                <span class="icon fa fa-font fa-3x"></span>
                <p>Text widget</p>
              </div>          <!-- content -->
              </a>
            </div>
            </div>          <!-- col-xs-2 -->
            <div class="col-md-2 col-sm-3 col-xs-4 no-padding ribbon">
              <div class="settingsWidget white-background">
              <a href="{{ URL::route('connect/new', ['provider' => 'chart', 'step' => 'init']) }}">
              <div class="settingsWidgetContent">
                <span class="icon fa fa-bar-chart fa-3x"></span>
                <p>Chart widget</p>
              </div>          <!-- content -->
              </a>
            </div>
            </div>          <!-- col-xs-2 -->
            <div class="col-md-2 col-sm-3 col-xs-4 no-padding ribbon">
              <div class="settingsWidget white-background">
              <a href="{{ URL::route('connect/new', ['provider' => 'list', 'step' => 'init']) }}">
              <div class="settingsWidgetContent">
                <span class="icon fa fa-list fa-3x"></span>
                <p>List widget</p>
              </div>          <!-- content -->
              </a>
            </div>
            </div>          <!-- col-xs-2 -->
            <div class="col-md-2 col-sm-3 col-xs-4 no-padding ribbon">
              <div class="settingsWidget white-background">
              <a href="{{ URL::route('connect.addwidget', 'iframe') }}">
              <div class="settingsWidgetContent">
                <span class="icon fa fa-file-text-o fa-3x"></span>
                <p>iframe</p>
              </div>          <!-- content -->
              </a>
            </div>
            </div>          <!-- col-xs-2 -->
            <div class="col-md-2 col-sm-3 col-xs-4 no-padding">
              <div class="settingsWidget white-background">
              <a href="{{ URL::route('connect.addwidget', 'quote') }}">
              <div class="settingsWidgetContent">
                <span class="icon fa fa-quote-left fa-3x"></span>
                <p>Quotes</p>
              </div>          <!-- content -->
              </a>
            </div>
            </div>          <!-- col-xs-2 -->            
            <div class="col-md-2 col-sm-3 col-xs-4 no-padding">
              <div class="settingsWidget white-background">
              <a href="{{ URL::route('connect.addwidget', 'note') }}">
              <div class="settingsWidgetContent">
                <span class="icon fa fa-pencil fa-3x"></span>
                <p>Notes</p>
              </div>          <!-- content -->
              </a>
            </div>
            </div>          <!-- col-xs-2 -->
            <div class="col-md-2 col-sm-3 col-xs-4 no-padding">
              <div class="settingsWidget white-background">
              <a href="{{ URL::route('connect.addwidget', 'greeting') }}">
              <div class="settingsWidgetContent">
                <span class="icon fa fa-comment-o fa-3x"></span>
                <p>Greetings</p>
              </div>          <!-- content -->
              </a>
            </div>
            </div>          <!-- col-xs-2 -->
            <div class="col-md-2 col-sm-3 col-xs-4 no-padding">
              <div class="settingsWidget white-background">
              <a href="{{ URL::route('connect.editwidget', 'background') }}">
              <div class="settingsWidgetContent">
                <span class="icon fa fa-picture-o fa-3x"></span>
                <p>Background</p>
              </div>          <!-- content -->
              </a>
            </div>
            </div>          <!-- col-xs-2 -->
            <div class="col-md-2 col-sm-3 col-xs-4 no-padding ribbon">
              <div class="settingsWidget white-background">
              <a href="{{ URL::route('connect/new', ['provider' => 'googlespreadsheet', 'step' => 'init']) }}">
              <div class="settingsWidgetContent">
                <span class="icon fa fa-google fa-3x"></span>
                <p>Google Spreadsheet</p>
              </div>          <!-- content -->
              </a>
            </div>
            </div>          <!-- col-xs-2 -->
            <div class="col-md-2 col-sm-3 col-xs-4 no-padding ribbon">
              <div class="settingsWidget white-background">
              <a href="{{ URL::route('connect.addwidget', 'api') }}">
              <div class="settingsWidgetContent">
                <span class="icon fa fa-code fa-3x"></span>
                <p>Webhook / API</p>
              </div>          <!-- content -->
              </a>
            </div>
            </div>          <!-- col-xs-2 -->

            {{-- 

            <div class="col-xs-2 col-xs-offset-1" style="padding-left: 0; padding-right: 0; opacity: 0.75; height:125px; background-color:white; margin-right:5px; margin-bottom:5px;">
              <a href="{{ $stripeButtonUrl }}">
              <div class="content" style="text-align:center; padding:30px; width:100%; height:100%;">
                <span class="icon pf-big pf-stripe"></span>
                <p>Stripe payments</p>
              </div>          <!-- content -->
              </a>
            </div>          <!-- col-xs-2 -->

            <div class="col-xs-2 col-xs-offset-1" style="padding-left: 0; padding-right: 0; opacity: 0.75; height:125px; background-color:white; margin-right:5px; margin-bottom:5px;">
              <a href="#" data-toggle='modal' data-target='#modal-braintree-connect'>
              <div class="content" style="text-align:center; padding:30px; width:100%; height:100%;">
                <span class="icon pf-big pf-braintree"></span>
                <p>Braintree payments</p>
              </div>          <!-- content -->
              </a>
            </div>          <!-- col-xs-2 -->

            --}}

            {{--
            
            <!-- Braintree details modal box -->
            <div id='modal-braintree-connect' class='modal fade in' tabindex='-1' role='dialog' style="display:none;" aria-hidden='true'>
              <div class='modal-dialog modal-lg'>
                <div>
                  <div class='modal-header'>
                    <button type="button" class="close" data-dismiss='modal' aria-hidden='true'>x</button>
                    <h4 class='modal-title'>Connect Braintree</h4>
                  </div>
                  <div class='modal-content' style='background:white;'>
                    @include('connect.connect-braintree',array('user'=>$user,'stepNumber'=>$braintree_connect_stepNumber))
                  </div>
                </div>
              </div>
            </div>
            <!-- /Braintree details modal box -->
            --}}
          </div>
        </div>          <!-- col-md-10 -->
    </div>          <!-- content-wrapper -->

  @stop

  @section('pageScripts')
    <!-- modal stuff for braintree start -->  

    @if (Session::has('modal'))
      <script type="text/javascript">
        $('#modal-braintree-connect').modal('show');
      </script>
    @endif

    <script type="text/javascript">
      init.push(function () {
        $('.ui-wizard').pixelWizard({
          onChange: function () {
            console.log('Current step: ' + this.currentStep());
          },
          onFinish: function () {
            // Disable changing step. To enable changing step just call this.unfreeze()
            this.freeze();
          }
        });

        $('.wizard-next-step-btn').click(function () {
          $(this).parents('.ui-wizard').pixelWizard('nextStep');
        });

        $('.wizard-prev-step-btn').click(function () {
          $(this).parents('.ui-wizard').pixelWizard('prevStep');
        });

        $('.wizard-go-to-step-btn').click(function () {
          $(this).parents('.ui-wizard').pixelWizard('setCurrentStep', 1);
        });
      });
    </script>

    <!-- modal stuff for braintree end -->
  
  @stop
