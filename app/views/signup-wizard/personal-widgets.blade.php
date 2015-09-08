@extends('meta.base-user')

  @section('pageTitle')
    Personal widgets
  @stop

  @section('pageStylesheet')
  @stop

  @section('pageContent') 
  <div class="container">
    
    <h1 class="text-center text-white drop-shadow">Select your default personal widgets</h1>

    <div class="row">
      <div class="col-md-10 col-md-offset-1">
        <div class="panel panel-default panel-transparent">
          <div class="panel-body">

            <!-- Form -->
            {{ Form::open(array('route' => 'signup-wizard.personal-widgets', 'id' => 'personal-widgets-form-id' )) }}
              
              <div class="row">
                <div class="col-md-5">
                  <div class="list-group margin-top-sm">
                    
                    <a href="#" class="list-group-item changes-image selected" data-widget="widget-clock">
                      Clock
                      <span class="fa fa-check text-success pull-right"></span>

                      <input id="widget-clock" name="widget-clock" type="checkbox" value="" class="not-visible" checked>
                    </a>

                    <a href="#" class="list-group-item changes-image selected" data-widget="widget-greetings">
                      Greetings
                      <span class="fa fa-check text-success pull-right"></span>

                      <input id="widget-greetings" name="widget-greetings" type="checkbox" value="" class="not-visible" checked>
                    </a>

                    <a href="#" class="list-group-item changes-image selected" data-widget="widget-quote">
                      Inspirational quotes
                      <span class="fa fa-check text-success pull-right"></span>

                      <input id="widget-quote" name="widget-quote" type="checkbox" value="" class="not-visible" checked>
                    </a>                    

                  </div>

                </div> <!-- /.col-md-5 -->
                <div class="col-md-7">
                  {{ HTML::image('img/demonstration/widget-clock.png', 'The Clock Widget', array('id' => 'img-change', 'class' => 'img-responsive img-rounded pull-right')) }}
                </div> <!-- /.col-md-7 -->
              </div> <!-- /.row -->

              <hr>
            
            <div class="row">
              <div class="col-md-12">
                <a href="{{ URL::route('signup-wizard.social-connections') }}" class="btn btn-warning">Back</a>
                {{ Form::submit('Finish' , array(
                    'id' => 'id_next',
                    'class' => 'btn btn-primary pull-right')) }}
                <a href="{{ URL::route('dashboard.dashboard') }}" class="btn btn-link pull-right">Skip, and finish</a>
              </div> <!-- / .col-md-12 -->
            </div> <!-- / .row -->

            {{ Form::close() }}
          </div> <!-- /.panel-body -->
        </div> <!-- /.panel -->
      </div> <!-- /.col-md-8 -->
    </div> <!-- /.row -->
  </div> <!-- /.container -->

  @stop

  @section('pageScripts')
  <script type="text/javascript">

    // Change the demonstration image on hover
    $(function(){
      var baseUrl = "/img/demonstration/";
      var ext = ".png";

      $('.changes-image').hover(
        //on mouse enter
        function() {
          //rewrite img src and change alternate text
          $('#img-change').attr('src', baseUrl + $(this).data('widget') + ext);
          $('#img-change').attr('alt', "The " + $(this).data('widget') + " Widget.");
        });
    });
  </script>

  <script type="text/javascript">
    
    // Select or deselect items by clicking
    $(function(){
      $('.list-group-item').click(function(){
        if ($(this).hasClass('selected')) {
          // change the icon
          $(this).find('span').attr('class', 'fa fa-times text-danger pull-right');
        } else {
          // change the icon
          $(this).find('span').attr('class', 'fa fa-check text-success pull-right');
        };
        // Toggle class
        $(this).toggleClass('selected');
        // Toggle hidden checkbox
        var checkbox = $('#' + $(this).data('widget'));
        checkbox.prop("checked", !checkbox.prop("checked"));
      });
    });
  </script>
  
  @stop
