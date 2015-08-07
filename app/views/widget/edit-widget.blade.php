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
            <div class="panel-body">
              <p class="lead text-center">
                Edit the settings of the
                <span class="text-success"><strong>{{ $widget->descriptor->name }} widget</strong></span>.
              </p>
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
                      {{ Form::select('dashboard', $dashboards, 'Dashboard', ['class' => 'form-control']) }}
                    </div>
                  </div>
                <!-- /.dashboard select -->
                <p class="text-center">
                  {{ Form::submit('Save settings', array('class' => 'btn btn-primary') ) }}
                </p>
              {{ Form::close() }}
            </div> <!-- /.panel-body -->
          </div> <!-- /.panel -->
        </div> <!-- /.col-md-8 -->
      </div> <!-- /.row -->
    </div> <!-- /.container -->
  </div> <!-- /.vertical-center -->

  @stop
