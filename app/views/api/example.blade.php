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
        <div class="col-md-12">
          <div class="panel panel-default panel-transparent">
            <div class="panel-body text-center">
              <h1>Your base API URL</h1>
              <p>{{ URL::route('api.post-data', array($apiVersion, $apiKey, $widgetID)) }}</p>
            </div> <!-- /.panel-body -->
          </div> <!-- /.panel -->
        </div> <!-- /.col-md-6 -->
      </div> <!-- /.row -->

      <div class="row">
        <div class="col-md-12">
          <div class="panel panel-default panel-transparent">
            <div class="panel-body">

              {{ Form::open(array(
                  'id'    => 'example-post-form',
                  'class' => 'form-horizontal' )) }}

                <div class="form-group">
                  {{ Form::label('url', 'POST url', array(
                    'class' => 'col-sm-3 control-label' )) }}

                  <div class="col-sm-6">
                    {{ Form::text('url', URL::route('api.post-data', array($apiVersion, $apiKey, $widgetID)), array(
                      'class' => 'form-control' )) }}
                  </div> <!-- /.col-sm-6 -->
                </div> <!-- /.form-group -->

                <div class="form-group">
                  {{ Form::label('json', 'Your data in JSON', array(
                    'class' => 'col-sm-3 control-label' )) }}

                  <div class="col-sm-6">
                    {{ Form::textarea('json', $defaultJSON, array(
                      'class' => 'form-control' )) }}
                  </div> <!-- /.col-sm-6 -->
                </div> <!-- /.form-group -->

                <div class="col-sm-12">
                  {{ Form::submit('Send data' , array(
                    'class' => 'btn btn-primary pull-right',
                    'data-loading-text' => 'Sending...' )) }}
                </div> <!-- /.col-sm-2 -->

              {{ Form::close() }}
            </div> <!-- /.panel-body -->
          </div> <!-- /.panel -->
        </div> <!-- /.col-md-12 -->
      </div> <!-- /.row -->

      <div class="row">
        <div class="col-md-12">
          <div class="panel panel-default panel-transparent">
            <div class="panel-body text-center">
              <div id="api-notification" class="list-group hidden">
                <h4 class="list-group-item-heading"></h4>
                <p class="list-group-item-text"></p>
              </div>
            </div> <!-- /.panel-body -->
          </div> <!-- /.panel -->
        </div> <!-- /.col-md-6 -->
      </div> <!-- /.row -->

    </div> <!-- /.container -->
  </div> <!-- /.vertical-center -->
  @stop

  @section('pageScripts')
  <script type="text/javascript">
    $("#example-post-form").submit(function(e) {
      e.preventDefault();

      var url = $(this).find('input[name=url]').val();
      var postData = $(this).find('textarea[name=json]').val();
      var notificationBox = $('#api-notification');
      var submitButton = $(this).find(':submit');

      // Change button text while sending
      submitButton.button('loading');

      // Call ajax function
      $.ajax({
        type: "POST",
        dataType: 'json',
        url: url,
             data: jQuery.parseJSON(postData.replace(/\'/g,'"')),
             success: function(data) {
                if (data.success) {
                  notificationBox.find('h4').text('Success');
                  notificationBox.find('p').text(data.success);
                  notificationBox.removeClass('hidden').fadeIn();
                } else if (data.error) {
                  notificationBox.find('h4').text('Error');
                  notificationBox.find('p').text(data.error);
                  notificationBox.removeClass('hidden').fadeIn();
                };

                // Reset button
                submitButton.button('reset');
             },
             error: function(){
                easyGrowl('error', "Something went wrong, we couldn't POST your data. Please try again.", 3000);
                // Reset button
                submitButton.button('reset');
             }
      });
    });
  </script>
  @stop
