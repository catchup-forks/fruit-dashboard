@extends('meta.base-user')

  @section('pageTitle')
    Financial connections
  @stop

  @section('pageStylesheet')
  @stop

  @section('pageContent')

  <div class="container">
    <h1 class="text-center text-white drop-shadow">
      Insert your Braintree API keys & Merchant ID
    </h1>
    <div class="row">
      <div class="col-md-10 col-md-offset-1">
        <div class="panel panel-default panel-transparent">
          <div class="panel-body">
            <div class="row">
              <div class="col-md-7">

                {{ Form::open(array(
                  'route' => array('service.braintree.connect'),
                  'class' => 'form-horizontal' )) }}

                @foreach ($authFields as $field)

                  <div class="form-group">

                    {{ Form::label($field, $field, array(
                      'class' => 'col-sm-3 control-label' )) }}

                    <div class="col-sm-6">

                      {{ Form::text($field, '', array('class' => 'form-control')) }}

                    </div> <!-- /.col-sm-6 -->

                  </div> <!-- /.form-group -->

                @endforeach

              </div> <!-- /.col-md-7 -->
              <div class="col-md-5">
                <ol>
                  <li>Go to your Braintree Control Panel and from Account in the top menu, select API Keys.</li>
                  <li>Select your Environment (either sandbox or production), copy and paste your Public and Private API keys and your Merchant ID into the relevant fields</li>
                </ol>
                <hr>

                <p class="text-danger">
                  For added security follow <a href="#" data-toggle="modal" data-target="#braintree-api">these steps</a> to create a read-only API key in your Braintree account.
                </p>

                <!-- Modal -->
                <div class="modal fade" id="braintree-api" tabindex="-1" role="dialog" aria-labelledby="braintree-api">
                  <div class="modal-dialog" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Creating a read-only API key with Braintree for added security</h4>
                      </div>
                      <div class="modal-body">
                        <p>For added security when using Braintree we recommend taking these steps to only grant read access to certain parts of your Braintree account.</p>

                        <ol>
                          <li>Login as admin to your Braintree account and go to Settings > Users and Roles > Manage Roles > New</li>
                          <li>Give the role a name like "Read only"</li>
                          <li>Uncheck all permissions except:
                            <ul>
                              <li>Download Transactions with Masked Payment Data</li>
                              <li>Download Vault Records with Masked Payment Data</li>
                              <li>Download Subscription Records</li>
                            </ul>
                          </li>
                          <li>Now click "Create role"</li>
                          <li>Now go to Settings > Users and roles > New user</li>
                          <li>Give the user API Access, assign the read only role and also access to the merchant accounts which you want to be included (usually all of them).</li>
                          <li>Now logout of Braintree and log back in as this new 'read only' user.</li>
                          <li>Then go to Account > My User > API Keys</li>
                          <li>Use these API keys</li>
                        </ol>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                      </div>
                    </div>
                  </div>
                </div>
              </div> <!-- /.col-md-5 -->
            </div> <!-- /.row -->

            <hr>
            <a href="{{ route('dashboard.dashboard') }}"><button type="button" class="btn btn-warning">Cancel</button></a>

            {{ Form::submit('Connect', array('class' => 'btn btn-primary pull-right')) }}

            {{ Form::close() }}

            </div> <!-- /.row -->
          </div> <!-- /.panel-body -->
        </div> <!-- /.panel -->
      </div> <!-- /.col-md-10 -->
    </div> <!-- /.row -->
  </div> <!-- /.container -->


  @stop

  @section('pageScripts')
  @stop

