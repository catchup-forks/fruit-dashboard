@extends('meta.base-user')

  @section('pageTitle')
    Personal widgets
  @stop

  @section('pageStylesheet')
  @stop

  @section('pageContent') 
    <div class="vertical-center">
        <div class="container">
            <!-- Form -->
            {{ Form::open(array('route' => 'signup-wizard.personal-widgets', 'id' => 'personal-widgets-form-id' )) }}
            
            <div class="form-actions text-center">
                {{ Form::submit('Next' , array(
                    'id' => 'id_next',
                    'class' => 'btn btn-primary btn-flat',
                    'onClick' => '')) }}
            </div> <!-- / .form-actions -->

            {{ Form::close() }}

        </div> <!-- /.container -->
    </div> <!-- /.vertical-center -->
  @stop

  @section('pageScripts')
  @stop
