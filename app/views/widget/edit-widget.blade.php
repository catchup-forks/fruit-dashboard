@extends('meta.base-user')

  @section('pageTitle')
    Edit widget settings
  @stop

  @section('pageContent')
  <div class="vertical-center">
    <div class="container">
      <div class="row">
        <div class="col-md-8 col-md-offset-2">
          <div class="panel panel-default panel-transparent">
            <div class="panel-heading">
              <h3 class="panel-title text-center">
                Edit the settings of the
                <span class="text-success"><strong>{{ $widget->getDescriptor()->name }} widget</strong></span>.
              </h3>
            </div> <!-- /.panel-heading -->
            <div class="panel-body">
              {{ Form::open(array('route' => array(
                'widget.edit',
                $widget->id),
                'class' => 'form-horizontal' )) }}

                @foreach ($widget->getSettingsFields() as $fieldSet=>$fields)
                @foreach ($fields as $field=>$meta)
                  @if ( $widget->isSettingVisible($field))
                    <div class="form-group" id="field-{{$field}}">
                    {{ Form::label($field, $meta['name'], array(
                        'class' => 'col-sm-3 control-label'
                      ))}}
                    <div class="col-sm-7">

                      @if ($meta['type'] == "SCHOICE" || $meta['type'] == "SCHOICEOPTGRP")
                        @if ((array_key_exists('disabled', $meta) && $meta['disabled'] == true))
                          <pre name="{{ $field }}">{{ $widget->$field() }}</pre>
                        @else
                          {{ Form::select($field, $widget->$field(), $widget->getSettings()[$field], ['class' => 'form-control', 'id' => $field . '-input']) }}
                        @endif

                      @elseif ($meta['type'] == "BOOL")
                      <!-- An amazing hack to send checkbox even if not checked -->
                        {{ Form::hidden($field, 0)}}
                        {{ Form::checkbox($field, 1, $widget->getSettings()[$field]) }}
                      @else
                        @if ((array_key_exists('disabled', $meta) && $meta['disabled'] == true))
                          <pre name="{{ $field }}">{{ $widget->getSettings()[$field] }}</pre>
                        @else
                          {{ Form::text($field, $widget->getSettings()[$field], ['class' => 'form-control', 'id' => $field . '-input']) }}
                        @endif
                      @endif
                      <div id="{{ $field }}-text">
                        @if ($errors->first($field))
                          <p class="text-danger">{{ $errors->first($field) }}</p>
                        @elseif (array_key_exists('help_text', $meta))
                          <p class="text-info">{{ $meta['help_text'] }}</p>
                        @endif
                      </div>
                    </div> <!-- /.col-sm-6 -->

                  </div> <!-- /.form-group -->
                  @endif

                @endforeach
                @endforeach
                <!-- dashboard select -->
                  <div class="form-group">
                    {{ Form::label('dashboard', 'Dashboard', array(
                        'class' => 'col-sm-3 control-label'
                      ))}}
                    <div class="col-sm-7">
                      {{ Form::select('dashboard', $dashboards, $widget->dashboard->id, ['class' => 'form-control']) }}
                      <p class="text-info">The widget will be displayed on this dashboard.</p>
                    </div>
                  </div>
                <!-- /.dashboard select -->
                <hr>
                  {{ Form::submit('Save', array(
                    'id' => 'save-settings',
                    'class' => 'btn btn-primary pull-right') ) }}
                  <a href="{{ route('dashboard.dashboard', $widget->dashboard) }}" class="btn btn-link pull-right">Cancel</a>
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
       * @listens | element(s): $('#save-settings') | event:click
       * --------------------------------------------------------------------------
       * Changes the button text to 'Loading...' when clicked
       * --------------------------------------------------------------------------
       */
       $('#save-settings').click(function() {
          $(this).button('loading');
       });

       @foreach ($widget->getSettingsFields() as $field=>$meta)
         @if ( array_key_exists('ajax_depends', $meta))
           /* Ajax loader for {{ $field }} */
           @if ($widget->$field() == false)
             $('#field-{{$field}}').hide();
           @endif
           $('#{{ $meta['ajax_depends'] }}-input').change(function () {
             $.ajax({
               type: 'get',
               url: '{{ route('widget.get-ajax-setting', array(
                 'widgetId'  => $widget->id,
                 'fieldName' => $field,
                 'value'     => 'field_value'
                 )) }}'.replace('field_value', $('#{{$meta['ajax_depends']}}-input').val()),
             }).done(function (data) {
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
