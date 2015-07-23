@extends('meta.base-user')

  @section('pageTitle')
    Financial connections
  @stop

  @section('pageStylesheet')
  @stop

  @section('pageContent')
    <div class="vertical-center">
        <div class="container">
            <!-- Form -->
            {{ Form::open(array('route' => 'signup-wizard.financial-connections', 'id' => 'personal-widgets-form-id' )) }}
            
            <div class="form-actions text-center">
                {{-- 
                {{ Form::submit('Connect your stripe account' , array(
                    'id' => 'id_stripe-connect',
                    'name'  => 'stripe-connect',
                    'class' => 'btn btn-primary btn-flat',
                    'onClick' => '')) }}
                --}}
                {{ Form::submit('Connect your braintree account' , array(
                    'id' => 'id_braintree-connect',
                    'name'  => 'braintree-connect',
                    'class' => 'btn btn-primary btn-flat',
                    'onClick' => '')) }}
            </div> <!-- / .form-actions -->

            {{ Form::close() }}
            <br>
            
            <div class="form-actions text-center">
                <a href="{{ StripeConnector::getStripeConnectURI(URL::route('signup-wizard.financial-connections')); }}" class="btn btn-primary btn-flat">
                  <span class="fa fa-arrow-right"></span> Stripe connection URL
                </a>
            <div class="form-actions text-center">
            <br>
            <div class="form-actions text-center">
                <a href="{{ URL::route('dashboard.dashboard') }}">
                  <span class="fa fa-arrow-right"></span> Skip / I'm finished
                </a>
            <div class="form-actions text-center">

        </div> <!-- /.container -->
    </div> <!-- /.vertical-center -->

  @stop

  @section('pageScripts')
  @stop
