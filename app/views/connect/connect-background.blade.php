@extends('meta.base-user')

@section('pageContent')

<div id="content-wrapper">
  @parent

        <!-- Appearance settings -->
        <div class="row">
          <div class="col-md-6 col-md-offset-3">
              <div class="panel-body bordered getHeight">
                <h4><i class="fa fa-picture-o"></i>&nbsp;&nbsp;Background settings</h4>

                <!-- Background switch -->
                {{ Form::open(array(
                  'action' => 'ConnectController@doSettingsBackground',
                  'id' => 'form-settings-background',
                  'role' => 'form',
                  'class' => 'form-horizontal' )) }}

                  <div id="editBackgroundForm">
                    <div class="form-group">
                      {{ Form::label('id_backgroundedit', 'Show Background', array(
                       'class' => 'col-sm-4 control-label')) }}
                      <div class="col-sm-8">
                        <p class="form-control-static">
                          @if (Auth::user()->isBackgroundOn)
                            <span>Yes</span>
                          @else
                            <span>No</span>
                          @endif
                          <button id="editBackground" class="btn btn-flat btn-info btn-sm pull-right" type="button" onClick= '_gaq.push(["_trackEvent", "Edit", "Editing background"]);mixpanel.track("Editing background");'>Edit</button>
                          <a href="{{ URL::route('connect.connect')}}">
                            {{ Form::button('Cancel', array(
                            'class' => 'btn btn-warning btn-sm btn-flat pull-right cancelButton'
                            )) }}
                          </a>
                        </p>
                      </div>
                    </div> <!-- / .form-group -->
                  </div>

                  <!-- hidden notification change form -->

                  <div id="changeBackgroundForm" class="hidden-form">
                    <div class="form-group @if ($errors->first('newBackgroundState')) has-error @endif">
                      {{ Form::label('id_background', 'Show Background', array(
                        'class' => 'col-sm-4 control-label')) }}
                      <div class="col-sm-8">
                        <div class="{{--switcher switcher-sm switcher-primary --}}@if (Auth::user()->isBackgroundOn)checked @endif">
                          {{ Form::checkbox('newBackgroundState',
                            Auth::user()->isBackgroundOn,
                            Auth::user()->isBackgroundOn,
                            array(                                       
                              'id' => 'id_background',
                              'class' => 'form-control',
//                              'data-class' => 'switcher-sm switcher-primary'
                            )) 
                          }}
                          
                        </div>
                      </div>
                    </div> <!-- / .form-group -->

                    <div class="col-sm-8 col-sm-offset-4 text-center padding-xs-vr">
                      <button class="btn btn-warning btn-sm btn-flat" type="button" id="cancelBackground">Cancel</button>  
                      {{ Form::submit('Save', array(
                        'id' => 'id_submit',
                        'class' => 'btn btn-primary btn-sm btn-flat',
                        'onClick'=> '_gaq.push(["_trackEvent", "Edit", "Background edited"]);
                        mixpanel.track("Background edited");')) }}
                    </div>

                  </div>
                {{ Form::close() }}
              </div>
            </div>
          </div>
        <!-- /Appearance settings -->


</div> <!-- / #content-wrapper -->

@stop

@section('pageScripts')


    <script type="text/javascript">
      init.push(function () {
        // event listeners for hidden forms
        $('#editBackground').on('click', function (){
          $('#editBackgroundForm').slideUp('fast', function (){
            $('#changeBackgroundForm').slideDown('fast');
          });
        })        
        $('#cancelBackground').on('click', function (){
          $('#changeBackgroundForm').slideUp('fast', function (){
            $('#editBackgroundForm').slideDown('fast');
          });
        })
      });

    </script>

@stop
