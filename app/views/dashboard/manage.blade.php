@extends('meta.base-user')

  @section('pageTitle')
    Manage dashboards
  @stop

  @section('pageStylesheet')
  @stop

  @section('pageContent')

  <div class="container">

    <h1 class="text-center text-white drop-shadow">
      Manage dashboards
    </h1>

    <div class="row">
      <div class="col-md-10 col-md-offset-1">
        <div class="panel panel-default panel-transparent">
          <div class="panel-body">
          @foreach (Auth::user()->dashboards as $dashboard)
            <div>
              {{ Form::text('dashboard', $dashboard->name) }}
              <span class="pull-right">
                <button><span class="icon fa fa-lock"></span></button>
                <button><span class="icon fa fa-trash"></span></a>
              </span>
            </div>
          @endforeach
          </div>
        </div>
      </div>
    </div>
  </div>
  @stop

  @section('pageScripts')

  @append

