<div class='wizard ui-wizard'>
  <div class='wizard-wrapper'>
    <ul class='wizard-steps' style='left:0px;'>
      <li data-target='#braintree-connect-step-1' @if($stepNumber == 1)class='active'@endif>
        <span class='wizard-step-number'>1</span>
        <span class='wizard-step-caption'>Step 1
          <span class='wizard-step-description'>Credentials</span>
        </span>
      </li>
      <li data-target='#braintree-connect-step-2' @if($stepNumber == 2)class='active'@endif>
        <span class='wizard-step-number'>2</span>
        <span class='wizard-step-caption'>Step 2
          <span class='wizard-step-description'>Keep synchronized</span>
        </span>
      </li>
      <li data-target='#braintree-connect-step-3' @if($stepNumber == 3)class='active'@endif>
        <span class='wizard-step-number'>3</span>
        <span class='wizard-step-caption'>Step 3
          <span class='wizard-step-description'>Import your data</span>
        </span>
      </li>
    </ul>
  </div> {{-- /wizard-wrapper --}}
  <div class='wizard-content'>
    <div class='wizard-pane' id='braintree-connect-step-1' @if($stepNumber == 1)style='display:block;opacity:1;'@endif>
      <div class='row'>
        <div class='col-sm-8 col-sm-offset-2'>
          <h3>First input and save your Braintree API keys</h3>
          <p class='text-danger small'>For added security follow the steps on the right to create a read-only API key in your Braintree account</p>
          {{ Form::open(array(
                  'action' => 'ConnectController@doBraintreeConnect',
                  'id' => 'form-connect-braintree',
                  'role' => 'form',
                  'class' => 'form-horizontal' )) }}
            
            <!-- Environment -->
            <div class="form-group">
              {{ Form::label('id_environment','Environment:', array(
                'class' => 'col-sm-3 control-label')) }}
              <div class="col-sm-4">
                {{ Form::select('environment',
                  // dropdown options
                  array(
                    'sandbox' => 'Sandbox', 
                    'production' => 'Production'),
                  // highlighted option
                  isset($user->btEnvironment)?$user->btEnvironment:'production',
                  array(                                       
                    'id' => 'id_environment',
                    'class' => 'form-control')) }}
              </div>
            </div>
            <!-- /Environment -->
            
            <!-- Public Key -->
            <div class='form-group'>
              {{ Form::label('id_publicKey','Public key:',array(
                'class' => 'col-sm-3 control-label')) }}
              <div class='col-sm-8'>
                {{ Form::text('publicKey',isset($user->btPublicKey)?$user->btPublicKey:'',array(
                  'id' => 'id_publicKey',
                  'class' => 'form-control',)) }}
              </div>
            </div>
            <!-- /Public Key -->

            <!-- Private Key -->
            <div class='form-group'>
              {{ Form::label('id_privateKey','Private key:',array(
                'class' => 'col-sm-3 control-label')) }}
              <div class='col-sm-8'>
                {{ Form::text('privateKey',isset($user->btPrivateKey)?$user->btPrivateKey:'',array(
                  'id' => 'id_privateKey',
                  'class' => 'form-control')) }}
              </div>
            </div>
            <!-- /Private Key -->

            <!-- Merchant Id -->
            <div class='form-group'>
              {{ Form::label('id_merchantId','Merchant ID:',array(
                'class' => 'col-sm-3 control-label')) }}
              <div class='col-sm-8'>
                {{ Form::text('merchantId',isset($user->btMerchantId)?$user->btMerchantId:'',array(
                  'id' => 'id_merchantId',
                  'class' => 'form-control')) }}
              </div>
            </div>
            <!-- /Merchant Id -->

            <!-- Submit -->
            <div class='form-group'>
              <div class='col-sm-2 col-sm-offset-5'>
                {{ Form::submit('Save API keys', array(
                    'id' => 'id_submit',
                    'class' => 'btn btn-primary btn-sm btn-flat text-center',)) }}

                <a href="{{ URL::route('connect.connect')}}">
                  {{ Form::button('Cancel', array(
                  'class' => 'btn btn-warning btn-sm btn-flat pull-right cancelButton'
                  )) }}
                </a>
              </div>
            </div>
            <!-- /Submit -->
          {{ Form::close() }}
          <div class='col-sm-2 col-sm-offset-7'>
            <button class='btn btn-default btn-sm btn-flat wizard-next-step-btn'>Webhook</button>
          </div>
        </div> {{-- /col-sm-8 --}}
      </div> {{-- /row --}}
      <div class='row'>
        @include('help.read-only')
      </div> {{-- /row --}}
    </div> {{-- /braintree-connect-step-1 --}}
    
    <div class='wizard-pane' id='braintree-connect-step-2' @if($stepNumber == 2)style='display:block;opacity:1;'@endif>
      <div class='row'>
        <div class='col-sm-8 col-sm-offset-2'>
          <h3>Add this webhook to keep your data synced</h3>
          <p class='top-space bottom-space well well-sm'>
            @if($user->isBraintreeCredentialsValid())
              <span class='text-danger'><strong>{{ URL::secure('api/events/braintree').'/'.$user->btWebhookId }}</strong></span>
            @else
              <span class='well well-sm text-danger'><strong>Save API keys first</strong></span>
            @endif
          </p>
          <div class='col-sm-7 col-sm-offset-2 top-space bottom-space'>
            <button class='btn btn-default btn-sm btn-flat wizard-prev-step-btn pull-left'>Credentials</button>
            <button class='btn btn-default btn-sm btn-flat wizard-next-step-btn pull-right'>Import</button>
          </div>
        </div> {{-- /col-sm-8 --}}
      </div> {{-- /row --}}
      <div class='row top-space'>
        @include('help.webhook-permissions')
      </div> {{-- /row --}}
    </div> {{-- /braintree-connect-step-2 --}}
    <div class='wizard-pane' id='braintree-connect-step-3' @if($stepNumber == 3)style='display:block;opacity:1;'@endif>
      <div class='row'>
        <div class='col-sm-8 col-sm-offset-2'>
          <div class='text-center'>
            <h3>To finish, import your data</h3>
            <a href="{{ URL::to('import/braintree') }}" class='btn btn-primary btn-lg'>Import</a>
          </div>
          <div class='col-sm-8 col-sm-offset-2 top-space bottom-space'>
            <button class='btn btn-default btn-sm btn-flat wizard-go-to-step-btn pull-left'>Credentials</button>
            <button class='btn btn-default btn-sm btn-flat wizard-prev-step-btn pull-right'>Webhook</button>
          </div>
        </div> {{-- /col-sm-8 --}}
      </div> {{-- /row --}}
    </div> {{-- /braintree-connect-step-3 --}}
  </div> {{-- /wizard-content --}}
</div> {{-- /wizard --}}


