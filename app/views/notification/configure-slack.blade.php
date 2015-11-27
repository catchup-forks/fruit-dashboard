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
                    <h3 class='text-center'>{{ ucwords($notification->type) }}</h3>

                    <div class="form-group">
                      {{ Form::label('address', 'Address', array(
                        'class' => 'col-sm-3 control-label' )) }}

                      <div class="col-sm-6">
                        <p class="form-control static">{{ $notification->address }}</p>                  
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

      <div class="row">
        <div class="col-md-12">
          <div class="panel panel-default panel-transparent">
            <div class="panel-body">
              <h2 class="text-center">Selected widgets for notifications</h2>
              <br>

              @foreach ($notifications as $notification) 
              {{ Form::open(array(
                  'route' => array('notification.widgets', $notification->id),
                  'id'    => 'notification-post-form',
                  'class' => 'form-horizontal' )) }}
                <h3 class='text-center'>{{ ucwords($notification->type) }}</h3>
                @foreach ($notification->user->dashboards as $dashboard)
                  <h4>Dashboard: {{ $dashboard->name }}</h4>
                  @foreach ($dashboard->widgets as $widget)
                    @if ($widget->canSendInNotification())
                      <div class="checkbox">
                        <label>
                          @if (is_null($notification->selected_widgets))
                            <input name="widget-{{ $widget->id }}" type="checkbox"> {{ $widget->getSettings()['name'] or 'No name provided for this widget (id=' . $widget->id . ')' }}</input>
                            <a class="btn btn-default btn-xs" href="{{ route('widget.to-image', [$widget->id]) }}">(download image)</a>
                          @else
                            @if (in_array($widget->id, json_decode($notification->selected_widgets)))
                              <input name="widget-{{ $widget->id }}" type="checkbox" checked> {{ $widget->getSettings()['name'] or 'No name provided for this widget (id=' . $widget->id . ')' }}</input>
                                <a class="btn btn-default btn-xs" href="{{ route('widget.to-image', [$widget->id]) }}">(download image)</a>
                            @else
                              <input name="widget-{{ $widget->id }}" type="checkbox"> {{ $widget->getSettings()['name'] or 'No name provided for this widget (id=' . $widget->id . ')' }}</input>
                              <a class="btn btn-default btn-xs" href="{{ route('widget.to-image', [$widget->id]) }}">(download image)</a>
                            @endif
                          @endif
                        </label>
                      </div>
                    @endif
                  @endforeach  
                @endforeach
                  <button type="submit" class="btn btn-default">Submit</button>
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
