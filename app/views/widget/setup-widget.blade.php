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
                  <span class="text-success"><strong>{{ $widget->getDescriptor()->name }} widget</strong></span>.
                </h3>
              </div> <!-- /.panel-heading -->
              <div class="panel-body">
                {{ Form::open(array('route' => array(
                  'widget.setup',
                  $widget->id),
                  'class' => 'form-horizontal' )) }}

                  @foreach ($settings as $field=>$meta)
                    <div class="form-group" id="field-{{$field}}">
                      {{ Form::label($field, $meta['name'], array(
                          'class' => 'col-sm-3 control-label'
                        )) }}
                      <div class="col-sm-8">
                        @if ($meta['type'] == "SCHOICE" || $meta['type'] == "SCHOICEOPTGRP")
                          {{ Form::select($field, $widget->$field(), null, ['class' => 'form-control', 'id' => $field . '-input']) }}
                        @elseif ($meta['type'] == "BOOL")
                        <!-- An amazing hack to send checkbox even if not checked -->
                          {{ Form::hidden($field, 0)}}
                          {{ Form::checkbox($field, 1, $widget->getSettings()[$field]) }}
                        @else
                          @if ((array_key_exists('disabled', $meta) && $meta['disabled'] == true))
                            <pre name="{{ $field }}">{{ $widget->getSettings()[$field] }}</pre>
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
                    <a href="{{ route('dashboard.dashboard', $widget->dashboard->id) }}" class="btn btn-link pull-right">Cancel</a>
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

       @foreach ($settings as $field=>$meta)
         @if ( array_key_exists('ajax_depends', $meta))
           /* Ajax loader for {{ $field }} */
           @if ($widget->$field() == false)
             $('#field-{{$field}}').hide();
           @endif
           $('#{{ $meta['ajax_depends'] }}-input').change(function () {
             $('#setup-widget').button('loading');
             $.ajax({
               type: 'get',
               url: '{{ route('widget.get-ajax-setting', array(
                 'widgetId'  => $widget->id,
                 'fieldName' => $field,
                 'value'     => 'field_value'
                 )) }}'.replace('field_value', $('#{{$meta['ajax_depends']}}-input').val()),
             }).done(function (data) {
               $('#setup-widget').button('reset');
               @if ($meta['type'] == 'SCHOICE')
                 $('#{{$field}}-input').empty();
                 if (data['error']) {
                   easyGrowl('error', data['error'], 3000);
                 } else {
                   $.each(data, function(id, name) {
                     $('#{{$field}}-input').append($("<option></option>")
                       .attr('value', id).text(name));
                   });
                 }

               @else
                $('#{{$field}}-input').val(data);
               @endif
              $('#field-{{$field}}').show();
             });
           });
         @endif
       @endforeach
    })
  </script>

  @append
