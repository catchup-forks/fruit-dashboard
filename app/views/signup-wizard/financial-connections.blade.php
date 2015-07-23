@extends('meta.base-user')

  @section('pageTitle')
    Financial connections
  @stop

  @section('pageStylesheet')
  @stop

  @section('pageContent')
    <div class="vertical-center">
        <div class="container">           
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
