@extends('meta.base-user-signout')

    @section('pageTitle')
        Signup | Authentication
    @stop

    @section('pageStylesheet')

    @stop

    @section('pageContent')

    <body style="background: url({{ Background::dailyBackgroundURL() }}) no-repeat center center fixed">

    <div class="vertical-center">
        <div class="container">
            <!-- name -->
            <div class="yourname-form">
                <h1 class="text-white text-center drop-shadow">
                    Good <span class="greeting"></span>.
                </h1>  
                <h1 class="text-white text-center drop-shadow">
                    What's your name?
                </h1>

            <!-- Form -->
            {{ Form::open(array('route' => 'signup-wizard.authentication', 'id' => 'signup-form-id' )) }}
            {{ Form::text('name', Input::old('name'), array('autofocus' => true, 'autocomplete' => 'off', 'class' => 'form-control input-lg text-white drop-shadow text-center greetings-name', 'id' => 'username_id')) }}
            </div>

            <!-- email -->
            <div class="youremail-form not-visible">
                <h1 class="text-white text-center drop-shadow">
                    Nice to meet you, <span class="username"></span>.
                </h1>  
                <h1 class="text-white text-center drop-shadow">
                    What is your email address?
                </h1>

                <!-- Stop Chrome from ignoring autocomplete -->
                <input style="display:none">
                <input type="password" style="display:none">
                
                <div class="form-group">
                    {{ Form::text('email', Input::old('email'), array('autocomplete' => 'off', 'autocorrect' => 'off', 'class' => 'form-control input-lg text-white drop-shadow text-center greetings-name', 'id' => 'email_id')) }}
                </div>
            </div>

            <!-- password -->
            <div class="yourpassword-form not-visible">
                <h1 class="text-white text-center drop-shadow">
                    …and you'll need a password.
                </h1>
                <div class="form-group">
                    {{ Form::password('password', array('autofocus' => true, 'autocomplete' => 'off', 'class' => 'form-control input-lg text-white drop-shadow text-center greetings-name', 'id' => 'password_id')) }}
                </div>
            </div>

            <div class="form-actions hidden-form not-visible">
                {{ Form::submit('Next' , array(
                    'id' => 'id_next',
                    'class' => 'btn btn-success pull-right',
                    'onClick' => '')) }}
            </div> <!-- / .form-actions -->

            {{ Form::close() }}

        </div> <!-- /.container -->
    </div> <!-- /.vertical-center -->

    </body>
    
    @stop

    @section('pageScripts')
    <script type="text/javascript">
        $(document).ready(function() {

          $('.greeting').html('{{ SiteConstants::getTimeOfTheDay() }}');

          
          $('#username_id').on('keydown', function (event){
            var keycode = (event.keyCode ? event.keyCode : event.which);
            if(keycode == '13' || keycode == '9'){
              event.preventDefault();
              $('.yourname-form').slideUp('fast', function (){
                $('.youremail-form').find('span.username').html(' ' + $('#username_id').val());
                $('.youremail-form').slideDown('fast', function() {
                  $('#email_id').focus();
                });
              });
            }    
          });

          $('#email_id').on('keydown', function (event){
            var keycode = (event.keyCode ? event.keyCode : event.which);
            if(keycode == '13' || keycode == '9'){
              event.preventDefault();
              $('.youremail-form').slideUp('fast', function (){
                $('.yourpassword-form').slideDown('fast', function() {
                  $('#password_id').focus();
                });
                $('.hidden-form').slideDown('fast', function() {
                });
              });
            }    
          });

          $('#password_id').on('keydown', function (event){
            
          });

          $('.greeting').html('{{ SiteConstants::getTimeOfTheDay() }}');
          
        });
    </script>

    @stop