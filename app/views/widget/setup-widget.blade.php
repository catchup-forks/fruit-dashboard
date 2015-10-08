@extends('meta.base-user')

  @section('pageTitle')
    Setup widget
  @stop

  @section('pageContent')
    <div class="vertical-center">
      <div class="container">
        <div class="row">
          <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default panel-transparent">
              <div class="panel-heading">
                <h3 class="panel-title text-center">
                  Setup the
                  <span class="text-success"><strong>{{ $widget->descriptor->name }} widget</strong></span>.
                </h3>
              </div> <!-- /.panel-heading -->
              <div class="panel-body">
                {{ Form::open(array('route' => array(
                  'widget.setup',
                  $widget->id),
                  'class' => 'form-horizontal' )) }}

                  @foreach ($settings as $field=>$meta)
                    <div class="form-group">
                      {{ Form::label($field, $meta['name'], array(
                          'class' => 'col-sm-3 control-label'
                        )) }}
                      <div class="col-sm-8">
                        @if ($meta['type'] == "SCHOICE" || $meta['type'] == "SCHOICEOPTGRP")
                          {{ Form::select($field, $widget->$field(), null, ['class' => 'form-control']) }}
                        @elseif ($meta['type'] == "BOOL")
                        <!-- An amazing hack to send checkbox even if not checked -->
                          {{ Form::hidden($field, 0)}}
                          {{ Form::checkbox($field, 1, $widget->getSettings()[$field]) }}
                        @else
                          @if ((array_key_exists('disabled', $meta) && $meta['disabled'] == TRUE))
                            <p name="{{ $field }}" class="form-control static">{{ $widget->getSettings()[$field] }}</p>
                          @else
                            {{ Form::text($field, $widget->getSettings()[$field], array(
                              'class' => 'form-control' )) }}
                          @endif
                        @endif
                        @if ($errors->first($field))
                          <p class="text-danger">{{ $errors->first($field) }}</p>
                        @elseif (array_key_exists('help_text', $meta))
                          <p class="text-info">{{ $meta['help_text'] }}</p>
                        @endif
                      </div> <!-- /.col-sm-7 -->
                    </div> <!-- /.form-group -->

                  @endforeach
                  <hr>
                    {{ Form::submit('Setup widget', array(
                      'id' => 'setup-widget',
                      'class' => 'btn btn-primary pull-right'
                      ) ) }}
                    <a href="{{ route('dashboard.dashboard', ['active' => $widget->dashboard->id]) }}" class="btn btn-link pull-right">Cancel</a>
                {{ Form::close() }}
              </div> <!-- /.panel-body -->
            </div> <!-- /.panel -->
          </div> <!-- /.col-md-8 -->
        </div> <!-- /.row -->
      </div> <!-- /.container -->
    </div> <!-- /.vertical-center -->

  @stop
  @section('pageScripts')

  <script type="text/javascript">
    $(document).ready(function(){

      /**
       * @listens | element(s): $('#setup-widget') | event:click
       * --------------------------------------------------------------------------
       * Changes the button text to 'Loading...' when clicked
       * --------------------------------------------------------------------------
       */
       $('#setup-widget').click(function() {
          $(this).button('loading');
       });

    })
  </script>

  @append