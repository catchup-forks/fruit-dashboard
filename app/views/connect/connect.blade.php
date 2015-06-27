@extends('meta.base-user')

  @section('pageTitle')
    Connect
  @stop

  @section('pageContent')
    <div id="content-wrapper">
        <div class="col-md-10 col-md-offset-1">

          @foreach ($widgetListArray as $widget)

            <div class="col-md-2 col-sm-3 col-xs-4 no-padding @if ($widget['premium']) ribbon @endif">
              <div class="settingsWidget white-background">
                <a href="{{ URL::route('connect/new', ['provider' => $widget['provider'], 'step' => 'init']) }}">
                  <div class="settingsWidgetContent">
                    <span class="icon fa {{ $widget['icon'] }} fa-3x"></span>
                    <p>{{ $widget['caption'] }}</p>
                  </div>
                </a>
              </div>
            </div>

          @endforeach





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
