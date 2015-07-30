@extends('meta.base-user')

  @section('pageTitle')
    Personal widgets
  @stop

  @section('pageStylesheet')
  @stop

  @section('pageContent') 
  <div class="container">
    
    <h1 class="text-center text-white drop-shadow">Select your personal widgets</h1>

    <div class="row">
      <div class="col-md-10 col-md-offset-1">
        <div class="panel panel-default panel-transparent">
          <div class="panel-body">
            <!-- Form -->
            {{ Form::open(array('route' => 'signup-wizard.personal-widgets', 'id' => 'personal-widgets-form-id' )) }}
              
              <div class="row">
                <div class="col-md-5">
                  <div class="checkbox changes-image" data-image="widget-clock">
                    <label>
                      <input name="widget-clock" type="checkbox" value="" checked>
                      Clock
                    </label>
                  </div>
                  <div class="checkbox changes-image" data-image="widget-greetings">
                    <label>
                      <input name="widget-greetings" type="checkbox" value="" checked>
                      Greetings
                    </label>
                  </div>
                  <div class="checkbox changes-image" data-image="widget-quote">
                    <label>
                      <input name="widget-quote" type="checkbox" value="" checked>
                      Inspirational quotes
                    </label>
                  </div>
                </div> <!-- /.col-md-5 -->
                <div class="col-md-7">
                  {{ HTML::image('img/demonstration/widget-clock.png', 'The Clock Widget', array('id' => 'img-change', 'class' => 'img-responsive')) }}
                </div> <!-- /.col-md-7 -->
              </div> <!-- /.row -->
            
            <div class="form-actions text-center">
                {{ Form::submit('Next' , array(
                    'id' => 'id_next',
                    'class' => 'btn btn-primary pull-right',
                    'onClick' => '')) }}
            </div> <!-- / .form-actions -->

            {{ Form::close() }}
          </div> <!-- /.panel-body -->
        </div> <!-- /.panel -->
      </div> <!-- /.col-md-8 -->
    </div> <!-- /.row -->
  </div> <!-- /.container -->

  @stop

  @section('pageScripts')
  <script type="text/javascript">
    $(function(){
      var baseUrl = "/img/demonstration/";
      var ext = ".png";

      $('.changes-image').hover(
        //on mouse enter
        function() {
          //rewrite img src and change alternate text
          $('#img-change').attr('src', baseUrl + $(this).data('image') + ext);
          $('#img-change').attr('alt', "The " + $(this).data('image') + " widget.");
        });
    });
  </script>
  
  @stop
