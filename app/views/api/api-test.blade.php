@extends('meta.base-user')

  @section('pageTitle')
    API Test
  @stop

  @section('pageStylesheet')
  @stop

  @section('pageContent')
  <div class="vertical-center">
    <div class="container">           
      <div class="row">
        <div class="col-md-12">
          <div class="panel panel-default panel-transparent">
            <div class="panel-body">
              <h2 class="text-center">Send data to your API widget</h2>
              <br>

              {{ Form::open(array(
                  'id'    => 'example-post-form',
                  'class' => 'form-horizontal' )) }}

                <div class="form-group">
                  {{ Form::label('url', 'POST url', array(
                    'class' => 'col-sm-3 control-label' )) }}

                  <div class="col-sm-6">
                    <p name="url" class="form-control static">{{ $url }}</p>
                  </div> <!-- /.col-sm-6 -->
                </div> <!-- /.form-group -->

                <div class="form-group">
                  {{ Form::label('json', 'Your data in JSON', array(
                    'class' => 'col-sm-3 control-label' )) }}

                  <div class="col-sm-6">
                    {{ Form::textarea('json', $defaultJSON, array(
                      'class' => 'form-control',
                      'rows'  => 5  )) }}
                  </div> <!-- /.col-sm-6 -->
                </div> <!-- /.form-group -->

                <div id="api-notification" class="text-center not-visible">
                  <div class="list-group ">
                    <h4 class="list-group-item-heading"></h4>
                    <p class="list-group-item-text"></p>
                  </div>
                </div> <!-- /.api-notification -->

                <div class="col-sm-12">
                  {{ Form::submit('Send data' , array(
                    'class' => 'btn btn-primary pull-right',
                    'data-loading-text' => 'Sending...' )) }}
                    <a class="btn btn-warning pull-left" href="{{ route('dashboard.dashboard',  $toDashboard) }}">Back to your widget</a>
                </div> <!-- /.col-sm-2 -->

              {{ Form::close() }}
            
            </div> <!-- /.panel-body -->
          </div> <!-- /.panel -->
        </div> <!-- /.col-md-12 -->
      </div> <!-- /.row -->

    </div> <!-- /.container -->
  </div> <!-- /.vertical-center -->
  @stop

  @section('pageScripts')
  <script type="text/javascript">
    $("#example-post-form").submit(function(e) {
      e.preventDefault();

      var url = $(this).find('p[name=url]').html();
      var postData = $(this).find('textarea[name=json]').val();
      var notificationBox = $('#api-notification');
      var submitButton = $(this).find(':submit');

      // Change button text while sending
      submitButton.button('loading');

      // Check the JSON
      try {
          jQuery.parseJSON(postData.replace(/\'/g,'"'))
      }
      catch(err) {
          // Add notification
          notificationBox.find('h4').text('Error');
          notificationBox.find('p').text("Your data cannot be parsed in JSON format.");
          notificationBox.fadeIn();
          // Reset button
          submitButton.button('reset');

          return false;
      }

      // Call ajax function
      $.ajax({
        type: "POST",
        dataType: 'json',
        url: url,
             data: jQuery.parseJSON(postData.replace(/\'/g,'"')),
             success: function(data) {
                if (data.status) {
                  // Add notification
                  notificationBox.find('h4').text('Success');
                  notificationBox.find('p').text(data.message);
                  notificationBox.fadeIn();
                } else {
                  // Add notification
                  notificationBox.find('h4').text('Error');
                  notificationBox.find('p').text(data.message);
                  notificationBox.fadeIn();
                };

                // Reset button
                submitButton.button('reset');
             },
             error: function(){
                // Add notification
                notificationBox.find('h4').text('Error');
                notificationBox.find('p').text("Something went wrong, we couldn't POST your data. Please try again.");
                notificationBox.fadeIn();

                // Reset button
                submitButton.button('reset');
             }
      });
    });
  </script>
  @stop
