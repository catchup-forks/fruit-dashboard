@extends('meta.base-user')

  @section('pageTitle')
    Notification Test
  @stop

  @section('pageStylesheet')
  @stop

  @section('pageContent')
  <div class="vertical-center">
    <div class="container">           
      <div class="row">
        <div class="col-md-12">
          <div class="panel panel-default panel-transparent">
            <div class="panel-body">
              <h2 class="text-center">Notification testing</h2>
              <br>

              @foreach ($notifications as $notification) 
                {{ Form::open(array(
                    'id'    => 'notifiaction-post-form',
                    'class' => 'form-horizontal' )) }}
                    <h3 class='text-center'>{{ $notification->type }}</h3>

                    <div class="form-group">
                      {{ Form::label('address', 'Address', array(
                        'class' => 'col-sm-3 control-label' )) }}

                      <div class="col-sm-6">
                        {{ Form::text('address', $notification->address, 
                            array('class' => 'form-control')) }}                      
                      </div> <!-- /.col-sm-6 -->

                      <div class="col-sm-3">
                        <a class='btn btn-primary' href="{{ route('notification.send', [$notification->id]) }}">Send</a>
                      </div> <!-- /.col-sm-3 -->
                    </div> <!-- /.form-group -->

                {{ Form::close() }}
              @endforeach

            </div> <!-- /.panel-body -->
          </div> <!-- /.panel -->
        </div> <!-- /.col-md-12 -->
      </div> <!-- /.row -->

    </div> <!-- /.container -->
  </div> <!-- /.vertical-center -->
  @stop

  @section('pageScripts')
  @stop
