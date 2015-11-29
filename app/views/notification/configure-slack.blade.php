@extends('meta.base-user')

  @section('pageTitle')
    Configure Slack Integration
  @stop

  @section('pageStylesheet')
  @stop

  @section('pageContent')
  
  <div class="container">
    <div class="row margin-top">
      <div class="col-md-10 col-md-offset-1">
        <div class="panel panel-default panel-transparent">
          <div class="panel-body">
            <span class="pull-right" title="Connect Fruit Dashboard to one of your Slack channels, and get daily notifications about your progress. <hr / > All you need is to set up a Slack webhook and paste the url in the box below." data-toggle="tooltip" data-placement="bottom">
              <sup>
                <i class="fa fa-2x fa-info-circle text-muted"></i>
              </sup>
            </span>
            <h1 class="text-center">
              Configure Slack Integration
            </h1>
            
            <div class="row margin-top">
                <div class="col-md-4 col-md-offset-4">
                  <div class="panel panel-default">
                    <div class="panel-body text-center">
                      {{ HTML::image('img/logos/slack.png', 'Chrome extension', array('class' => 'img-responsive img-rounded')) }}
                      
                      @if($notification->is_enabled)
                        <p class="text-success text-center lead margin-top">
                          <span class="fa fa-check"> </span> Connection is active
                        </p>
                      @else
                        <p class="text-danger text-center lead margin-top">
                          <span class="fa fa-times"> </span> Connection is not active
                        </p>
                      @endif

                    </div> <!-- /.panel-body -->
                  </div> <!-- /.panel -->  
                </div> <!-- /.col-md-4 -->
            </div> <!-- /.row -->
            
            <form method="POST" action="{{ route('notification.configureSlack') }}" class="form-horizontal">
              <div class="row margin-top">
                <div class="col-md-12">
                  <div class="form-group">
                    <div class="col-sm-10 col-sm-offset-1">
                      <input name='address' type='text' class='form-control' placeholder='https://hooks.slack.com/services/<tokens>' value="{{ $notification->address }}">
                    </div> <!-- /.col-sm-10 -->  
                  </div> <!-- /.form-group -->
                </div> <!-- /.col-md-12 -->
              </div> <!-- /.row -->

              <hr>

              <div class="row">
                <div class="col-md-12">
                  <div class="panel panel-default panel-transparent">
                    <div class="panel-body">
                      <h2 class="text-center">Selected widgets</h2>
                      <br>
                        @foreach ($notification->user->dashboards as $dashboard)
                          <h4>Dashboard: {{ $dashboard->name }}</h4>
                          @foreach ($dashboard->widgets as $widget)
                            @if ($widget->canSendInNotification())
                              <div class="checkbox">
                                <label>
                                  @if (is_null($notification->selected_widgets))
                                    <input name="widget-{{ $widget->id }}" type="checkbox"> {{ $widget->getSettings()['name'] or 'No name provided for this widget (id=' . $widget->id . ')' }}</input>
                                  @else
                                    @if (in_array($widget->id, json_decode($notification->selected_widgets)))
                                      <input name="widget-{{ $widget->id }}" type="checkbox" checked> {{ $widget->getSettings()['name'] or 'No name provided for this widget (id=' . $widget->id . ')' }}</input>
                                    @else
                                      <input name="widget-{{ $widget->id }}" type="checkbox"> {{ $widget->getSettings()['name'] or 'No name provided for this widget (id=' . $widget->id . ')' }}</input>
                                    @endif
                                  @endif
                                </label>
                              </div>
                            @endif
                          @endforeach  
                        @endforeach
                    </div> <!-- /.panel-body -->
                  </div> <!-- /.panel -->

                </div> <!-- /.col-md-12 -->
              </div> <!-- /.row -->

              <div class="row">
                <div class="col-md-12 text-center">
                  <a href="{{ route('dashboard.dashboard') }}" class="btn btn-warning pull-left">Cancel</a>
                  <button type="submit" class='btn btn-primary pull-right'>Save</button>
                  @if ($notification->is_enabled)
                    <a href="{{ route('notification.sendSlackMessage') }}" class='btn btn-info'>Send message to slack</a>
                  @endif
                </div> <!-- /.col-md-12 -->
              </div> <!-- /.row -->
            </form>

          </div> <!-- /.panel-body -->
        </div> <!-- /.panel -->
      </div> <!-- /.col-md-10 -->
    </div> <!-- /.row -->
  </div> <!-- /.container -->
  @stop

  @section('pageScripts')
  @stop
