@extends('meta.base-user')

  @section('pageTitle')
    Connect
  @stop

  @section('pageContent')
    
    <div id="content-wrapper" class="gridster">

      <ul>

        <li class="dashboard-widget well text-center white-background" data-row="1" data-col="1" data-sizex="1" data-sizey="1">
        @if ($user->canConnectMore())
          <a href="{{ $stripeButtonUrl }}">
        @else
          <a href="/plans">
        @endif
            <span class="icon pf-big pf-stripe"></span>
            <p>Stripe payments</p>
          </a>
        </li>

        <li class="dashboard-widget well text-center white-background" data-row="1" data-col="2" data-sizex="1" data-sizey="1">
          <a href="#" data-toggle='modal' data-target='#modal-braintree-connect'>
            <span class="icon pf-big pf-braintree"></span>
            <p>Braintree payments</p>
          </a>
        </li>

        <li class="dashboard-widget well text-center white-background" data-row="1" data-col="3" data-sizex="1" data-sizey="1">
        @if ($user->isGoogleSpreadsheetConnected())
          <a href="{{ URL::route('connect.addwidget', 'googlespreadsheet') }}">
        @elseif($user->canConnectMore())
          <a href="{{ $googleSpreadsheetButtonUrl }}" onclick='_gaq.push(["_trackEvent", "Connect", "Connecting Google Spreadsheet"]);mixpanel.track("Google Spreadsheet connect");'>
        @else
          <a href="/plans">
        @endif
            <span class="icon fa fa-google fa-3x"></span>
            <p>Google Spreadsheet</p>
          </a>
        </li>


        <li class="dashboard-widget well text-center white-background" data-row="1" data-col="4" data-sizex="1" data-sizey="1">
        @if ($user->canConnectMore())
          <a href="{{ URL::route('connect.addwidget', 'iframe') }}">
        @else
          <a href="/plans">
        @endif
            <span class="icon fa fa-file-text-o fa-3x"></span>
            <p>iframe</p>
          </a>
        </li>


        <li class="dashboard-widget well text-center white-background" data-row="2" data-col="1" data-sizex="1" data-sizey="1">
        @if ($user->canConnectMore())
          <a href="{{ URL::route('connect.addwidget', 'quote') }}">
        @else
          <a href="/plans">
        @endif
            <span class="icon fa fa-quote-left fa-3x"></span>
            <p>Quotes</p>
          </a>
        </li>


      </ul>

      <!-- Braintree details modal box -->
      <div id='modal-braintree-connect' class='modal fade in' tabindex='-1' role='dialog' style="display:none;" aria-hidden='true'>
        <div class='modal-dialog modal-lg'>
          <div>
            <div class='modal-header'>
              <button type="button" class="close" data-dismiss='modal' aria-hidden='true'>x</button>
              <h4 class='modal-title'>Connect Braintree</h4>
            </div>
            <div class='modal-content' style='background:white;'>
              @include('connect.braintreeConnect',array('user'=>$user,'stepNumber'=>$braintree_connect_stepNumber))
            </div>
          </div>
        </div>
      </div>
      <!-- /Braintree details modal box -->

    </div> <!-- / #content-wrapper -->

  @stop

  @section('pageScripts')

    <script type="text/javascript">
      $(document).ready(function() {
          var gridster;
          var widget_width = $(window).width()/6-15;
          var widget_height = $(window).height()/6-20;

          $(function(){

            gridster = $(".gridster ul").gridster({
              widget_base_dimensions: [widget_width, widget_height],
              widget_margins: [5, 5],
              helper: 'clone',
              resize: {
                enabled: true,
                max_size: [4, 4],
                min_size: [1, 1]
              }
            }).data('gridster');

          });
      });
    </script>


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
