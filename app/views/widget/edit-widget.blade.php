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

                  <div class="form-group">
                    {{ Form::label($field, $meta['name'], array(
                        'class' => 'col-sm-3 control-label'
                      ))}}
                    <div class="col-sm-7">
                      @if ($meta['type'] == "SCHOICE")
                        {{ Form::select($field, $widget->$field(), $widget->getSettings()[$field], ['class' => 'form-control']) }}
                      @elseif ($meta['type'] == "BOOL")
                      <!-- An amazing hack to send checkbox even if not checked -->
                        {{ Form::hidden($field, 0)}}
                        {{ Form::checkbox($field, 1, $widget->getSettings()[$field]) }}
                      @else
                        {{ Form::text($field, $widget->getSettings()[$field], array(
                      'class' => 'form-control' )) }}
                      @endif
                    </div> <!-- /.col-sm-6 -->

                  </div> <!-- /.form-group -->

                @endforeach
                <!-- dashboard select -->
                  <div class="form-group">
                    {{ Form::label('dashboard', 'Dashboard', array(
                        'class' => 'col-sm-3 control-label'
                      ))}}
                    <div class="col-sm-7">
                      {{ Form::select('dashboard', $dashboards, $widget->dashboard->id, ['class' => 'form-control']) }}
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
                      {{ Form::text('update_period', $widget->dataManager()->update_period, ['class' => 'form-control']) }}
                    </div>
                  </div>
                @endif
                <!-- /.Update interval select -->
                <hr>
                  {{ Form::submit('Save', array('class' => 'btn btn-primary pull-right') ) }}
                  <a href="/" class="btn btn-link pull-right">Cancel</a>
              {{ Form::close() }}
            </div> <!-- /.panel-body -->
          </div> <!-- /.panel -->
        </div> <!-- /.col-md-8 -->
      </div> <!-- /.row -->
    </div> <!-- /.container -->
  </div> <!-- /.vertical-center -->

  @stop
