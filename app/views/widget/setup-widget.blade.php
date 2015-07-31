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
              <div class="panel-body">
                <p class="lead text-center">
                  Setup the
                  <span class="text-success"><strong>{{ $widget->descriptor->name }} widget</strong></span>.
                </p>
                {{ Form::open(array('route' => array(
                  'widget.setup',
                  $widget->id),
                  'class' => 'form-horizontal' )) }}

                  @foreach ($settings as $field=>$meta)

                    <div class="form-group">
                      {{ Form::label($field, $meta['name'], array(
                          'class' => 'col-sm-3 control-label'
                        ))}}
                      <div class="col-sm-7">
                        @if ($meta['type'] == "SCHOICE")
                          {{ Form::select($field, $widget->$field(), null, ['class' => 'form-control']) }}
                        @else
                          {{ Form::text($field, $widget->getSettings()[$field], array(
                        'class' => 'form-control' )) }}
                        @endif
                      </div> <!-- /.col-sm-6 -->

                    </div> <!-- /.form-group -->

                  @endforeach
                  <p class="text-center">
                    {{ Form::submit('Setup widget', array('class' => 'btn btn-primary') ) }}
                  </p>
                {{ Form::close() }}
              </div> <!-- /.panel-body -->
            </div> <!-- /.panel -->
          </div> <!-- /.col-md-8 -->
        </div> <!-- /.row -->
      </div> <!-- /.container -->
    </div> <!-- /.vertical-center -->

  @stop