@extends('meta.base-user')

  @section('pageTitle')
    Unsubscribe
  @stop

  @section('pageStylesheet')
  @stop

  @section('pageContent')
    <div class="vertical-center">
      <div class="container">

        <div class="row">
          <div class="col-md-6 col-md-offset-3">
            <div class="panel panel-default panel-transparent">
              <div class="panel-body text-center">
                {{ Form::open(array('route' => 'payment.unsubscribe')) }}
                {{ Form::submit('Unsubscribe, and continue with free plan' , array(
                    'class' => 'btn btn-success btn-block')) }}
                {{ Form::close() }}
              </div> <!-- /.panel-body -->
            </div> <!-- /.panel -->
          </div> <!-- /.col-md-6 -->
        </div> <!-- /.row -->

      </div> <!-- /.container -->
    </div> <!-- /.vertical-center -->
  @stop

  @section('pageScripts')
  @stop



