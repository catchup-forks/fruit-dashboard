@extends('meta.base-user')

  @section('pageTitle')
    Financial connections
  @stop

  @section('pageStylesheet')
  @stop

  @section('pageContent')
  <div class="container">
    <h1 class="text-center text-white drop-shadow">
      Select your google analytics properties.
    </h1>
    <div class="row">
      <div class="col-md-10 col-md-offset-1">
        <div class="panel panel-default panel-transparent">
          <div class="panel-body">
            <div class="row">

              {{ Form::open(array(
                'route' => array('service.google-analytics.select-properties'))) }}

              {{ Form::label('properties', 'Google properties')}}
              {{ Form::select('properties[]', $properties, null, array('multiple'))}}

            {{ Form::submit('I choose you') }}
            {{ Form::close() }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  @stop

  @section('pageScripts')
  @stop
