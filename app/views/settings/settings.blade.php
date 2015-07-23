@extends('meta.base-user')

  @section('pageTitle')
    Account settings
  @stop

  @section('pageStylesheet')
  @stop

  @section('pageContent')
    <div class="vertical-center">
      <div class="container">           
        {{ $user }}
        {{ $settings }}
        {{ $subscription }}

        <br>

          {{ Form::open(array('route' => array('settings.change', 'name'), 'id' => 'settings-form' )) }}

          <div class="form-actions text-center">
              {{ Form::submit('Change name' , array(
                  'id' => 'id_name',
                  'class' => 'btn btn-success btn-flat',
                  'onClick' => '')) }}
              {{ Form::text('name', Input::old('name'), array('autofocus' => true, 'autocomplete' => 'off', 'class' => 'form-control input-lg text-white drop-shadow text-center greetings-name', 'id' => 'username_id')) }}
          </div> <!-- / .form-actions -->

          {{ Form::close() }}

        <br>

          {{ Form::open(array('route' => array('settings.change', 'email'), 'id' => 'settings-form' )) }}

          <div class="form-actions text-center">
              {{ Form::submit('Change email' , array(
                  'id' => 'id_name',
                  'class' => 'btn btn-success btn-flat',
                  'onClick' => '')) }}
          </div> <!-- / .form-actions -->
          {{ Form::close() }}


      </div> <!-- /.container -->
    </div> <!-- /.vertical-center -->

  @stop

  @section('pageScripts')
  @stop