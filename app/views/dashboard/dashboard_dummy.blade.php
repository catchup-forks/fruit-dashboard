@extends('meta.base-user')

@section('pageTitle')
  Dashboard
@stop

@section('pageStylesheet')
@stop

@section('pageContent')
<div class="container">
  <div class="row">
    <div class="col-md-12">
      <div class="panel panel-default panel-transparent">
        <div class="panel-body">
          <pre>
 {{ var_dump($dashboard); }}
          </pre>
        </div>
      </div>
    </div>
  </div>
</div>



@if (GlobalTracker::isTrackingEnabled() and Input::get('tour'))
  @include('dashboard.dashboard-google-converison-scripts')
@endif


@stop

@section('pageScripts')
@append

