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
              {{ Form::open(array('route' => array('signup-wizard.braintree-connect'))) }}
              @foreach (BraintreeConnector::$authFields as $field)
                {{$field}}
                {{ Form::text($field) }}
                <br>
              @endforeach
              {{ Form::submit('Submit!') }}
              {{ Form::close() }}
            </div>
        </div> <!-- /.container -->
    </div> <!-- /.vertical-center -->

  @stop

  @section('pageScripts')
  @stop
