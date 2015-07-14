@extends('meta.base-user')

  @section('pageTitle')
    Dashboard
  @stop

  @section('pageStylesheet')
  @stop

  @section('pageContent')

  <!-- add new widget -->
  <div class="position-bl drop-shadow z-top">
    <a href="{{ URL::route('connect.connect') }}" alt="Add new widget" title="Add new widget">
      <span class="fa fa-plus-circle fa-2x fa-inverse color-hovered"></span>
    </a>  
  </div>

  <!-- widget list -->
  <div class="container">
    <div class="row">
      <div class="gridster not-visible">
        <ul>
          @for ($i = 0; $i < count($allFunctions); $i++)

            @include('dashboard.widget', ['widget_data' => $allFunctions[$i]])

          @endfor
        </ul>
      </div>
    </div>
  </div>
  <!-- /widget list -->
  
  @stop

  @section('pageScripts')

  @append

