@extends('meta.base-user')

  @section('pageTitle')
    API Example
  @stop

  @section('pageStylesheet')
  @stop

  @section('pageContent')
  <div class="vertical-center">
    <div class="container">           
      <div class="row">
        <div class="col-md-6 col-md-offset-3">
          <div class="panel panel-default panel-transparent">
            <div class="panel-body text-center">
              <h1>Your base API URL</h1>
              <p>{{ URL::route('api.save-data', array($apiVersion, $apiKey, $widgetID)) }}</p>
            </div> <!-- /.panel-body -->
          </div> <!-- /.panel -->
        </div> <!-- /.col-md-6 -->

      </div> <!-- /.row -->
    </div> <!-- /.container -->
  </div> <!-- /.vertical-center -->
  @stop

  @section('pageScripts')
  @stop
