@extends('meta.base-user')

  @section('pageTitle')
    Account settings
  @stop

  @section('pageStylesheet')
  @stop

  @section('pageContent')
    <div class="container">
      <div class="row">
        <div class="col-md-8 col-md-offset-2">
          <div class="panel panel-default panel-transparent margin-top">
            <div class="panel-heading">
              <h3 class="panel-title">
                <span class="fa fa-cog"></span>
                Account settings
              </h3>
            </div> <!-- /.panel-heading -->
            <div class="panel-body">


              {{-- Account settings - Username --}}
              {{-- START --}}
              {{ Form::open(array(
                  'data-setting-name' => 'name', 
                  'class' => 'form-horizontal settings-form' )) }}

                <div class="form-group">

                  {{ Form::label('name', 'Username', array(
                    'class' => 'col-sm-3 control-label' )) }}

                  <div class="col-sm-6">

                    {{ Form::text('name', $user->name, array(
                      'class' => 'form-control' )) }}

                  </div> <!-- /.col-sm-6 -->

                  <div class="col-sm-2">
                    
                    {{ Form::submit('Modify' , array(
                      'class' => 'btn btn-primary' )) }}

                  </div> <!-- /.col-sm-2 -->
                </div> <!-- /.form-group -->

              {{ Form::close() }}
              {{-- END --}}
              {{-- Account settings - Username  --}}

              {{-- Account settings - E-mail --}}
              {{-- START --}}
              {{ Form::open(array(
                  'data-setting-name' => 'email', 
                  'class' => 'form-horizontal settings-form' )) }}

                <div class="form-group">

                  {{ Form::label('email', 'E-mail', array(
                    'class' => 'col-sm-3 control-label' )) }}

                  <div class="col-sm-6">

                    {{ Form::text('email', $user->email, array(
                      'class' => 'form-control' )) }}

                  </div> <!-- /.col-sm-6 -->

                  <div class="col-sm-2">
                    
                    {{ Form::submit('Modify' , array(
                      'class' => 'btn btn-primary' )) }}

                  </div> <!-- /.col-sm-2 -->
                </div> <!-- /.form-group -->

              {{ Form::close() }}
              {{-- END --}}
              {{-- Account settings - E-mail  --}}

              {{-- Account settings - Password --}}
              {{-- START --}}
              {{--
              {{ Form::open(array('route' => array('settings.change', 'password'), 'class' => 'form-horizontal' )) }}

                <div class="form-group">

                  {{ Form::label('password', 'Password', array(
                    'class' => 'col-sm-3 control-label' )) }}

                  <div class="col-sm-6">

                    {{ Form::password('password', array(
                      'class' => 'form-control' )) }}

                  </div> <!-- /.col-sm-6 -->

                  <div class="col-sm-2">
                    
                    {{ Form::submit('Modify' , array(
                      'class' => 'btn btn-primary' )) }}

                  </div> <!-- /.col-sm-2 -->
                </div> <!-- /.form-group -->

              {{ Form::close() }}
              --}}
              {{-- END --}}
              {{-- Account settings - Password  --}}


            </div> <!-- /.panel-body -->
          </div> <!-- /.panel -->
        </div> <!-- /.col-md-6 -->
      </div> <!-- /.row -->
    </div> <!-- /.container -->

  {{-- 
    {{ $user }}
    {{ $settings }}
    {{ $subscription }}
   --}}

  @stop

  @section('pageScripts')
    <script type="text/javascript">
      $(".settings-form").submit(function(e) {
        e.preventDefault();

        // initialize url
        var form = $(this)
        var url = "{{ route('settings.change', 'setting-name') }}".replace('setting-name', $(this).attr("data-setting-name"))
        var button = ""

        // Switch submit button to spinner
        form.find(':submit').hide();
        form.find(':submit').before($('<i/>', {'class': 'fa fa-spinner fa-spin fa-2x',}));
        
        // Call ajax function
        $.ajax({
          type: "POST",
          dataType: 'json',
          url: url,
               data: form.serialize(),
               success: function(data) {
                  if (data.success) {
                    $.growl.notice({
                      title: "Success!",
                      message: data.success,
                      size: "large",
                      duration: 3000,
                      location: "br"
                    });
                  } else if (data.error) {
                    $.growl.error({
                      title: "Error!",
                      message: data.error,
                      size: "large",
                      duration: 3000,
                      location: "br"
                    });
                  };

                  // Switch spinner to button
                  form.find('.fa-spinner').hide();
                  form.find(':submit').show();
               },
               error: function(){
                  $.growl.error({
                    title: "Error!",
                    message: "Something went wrong, we couldn't edit your settings. Please try again.",
                    size: "large",
                    duration: 3000,
                    location: "br"
                  });
                  
                  // Switch spinner to button
                  form.find('.fa-spinner').hide();
                  form.find(':submit').show();
               }
        });
      });
    </script>
  @stop