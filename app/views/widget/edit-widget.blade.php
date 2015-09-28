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
                <span class="text-success"><strong>{{ $widget->descriptor->name }} widget</strong></span>.
              </h3>
            </div> <!-- /.panel-heading -->
            <div class="panel-body">
              {{ Form::open(array('route' => array(
                'widget.edit',
                $widget->id),
                'class' => 'form-horizontal' )) }}

                @foreach ($widget->getSettingsFields() as $field=>$meta)
                  @if ( ! array_key_exists('hidden', $meta) || $meta['hidden'] == FALSE)
                  <div class="form-group">
                    {{ Form::label($field, $meta['name'], array(
                        'class' => 'col-sm-3 control-label'
                      ))}}
                    <div class="col-sm-7">
                        @if ($meta['type'] == "SCHOICE" || $meta['type'] == "SCHOICEOPTGRP")
                        @if ((array_key_exists('disabled', $meta) && $meta['disabled'] == TRUE))
                          <p name="{{ $field }}" class="form-control static">{{ $widget->$field() }}</p>
                        @else
                          {{ Form::select($field, $widget->$field(), $widget->getSettings()[$field], ['class' => 'form-control']) }}
                        @endif

                      @elseif ($meta['type'] == "BOOL")
                      <!-- An amazing hack to send checkbox even if not checked -->
                        {{ Form::hidden($field, 0)}}
                        {{ Form::checkbox($field, 1, $widget->getSettings()[$field]) }}
                      @else
                        @if ((array_key_exists('disabled', $meta) && $meta['disabled'] == TRUE))
                          <p name="{{ $field }}" class="form-control static">{{ $widget->getSettings()[$field] }}</p>
                        @else
                          {{ Form::text($field, $widget->getSettings()[$field], ['class' => 'form-control']) }}
                        @endif
                      @endif
                      @if ($errors->first($field))
                        <p class="text-danger">{{ $errors->first($field) }}</p>
                      @elseif (array_key_exists('help_text', $meta))
                        <p class="text-info">{{ $meta['help_text'] }}</p>
                      @endif
                    </div> <!-- /.col-sm-6 -->

                  </div> <!-- /.form-group -->
                  @endif

                @endforeach
                <!-- dashboard select -->
                  <div class="form-group">
                    {{ Form::label('dashboard', 'Dashboard', array(
                        'class' => 'col-sm-3 control-label'
                      ))}}
                    <div class="col-sm-7">
                      {{ Form::select('dashboard', $dashboards, $widget->dashboard->id, ['class' => 'form-control']) }}
                      <p class="text-info">The widget will be assigned to this dashboard.</p>
                    </div>
                  </div>
                <!-- /.dashboard select -->
                @if ($widget instanceof CronWidget)
                  <!-- Update interval select -->
                  <div class="form-group">
                    {{ Form::label('update_period', 'Update (Minutes)', array(
                        'class' => 'col-sm-3 control-label'
                      ))}}
                    <div class="col-sm-7">
                      {{ Form::text('update_period', $widget->getUpdatePeriod(), ['class' => 'form-control']) }}
                      <p class="text-info">The number of minutes the widget data will be updated automatically. (min. 30)</p>
                    </div>
                  </div>
                @endif
                <!-- /.Update interval select -->
                <hr>
                  {{ Form::submit('Save', array('class' => 'btn btn-primary pull-right') ) }}
                  <a href="{{ route('dashboard.dashboard', ['active' => $widget->dashboard->id]) }}" class="btn btn-link pull-right">Cancel</a>
              {{ Form::close() }}
            </div> <!-- /.panel-body -->
          </div> <!-- /.panel -->
        </div> <!-- /.col-md-8 -->
      </div> <!-- /.row -->
    </div> <!-- /.container -->
  </div> <!-- /.vertical-center -->

  @stop
