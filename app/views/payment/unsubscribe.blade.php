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
                <div class="form-actions text-center">
                    {{ Form::submit('Unsubscribe, and continue with free plan' , array(
                    'class' => 'btn btn-success')) }}
                </div> <!-- / .form-actions -->
                {{ Form::close() }}
              </div> <!-- /.panel-body -->
              <div class="panel-footer text-center">
                You are about to unsubscribe. <br>
                The premium funcionalities can be reactivated any time if you subscribe again.
              </div> <!-- /.panel-footer -->
            </div> <!-- /.panel -->
          </div> <!-- /.col-md-6 -->
        </div> <!-- /.row -->

      </div> <!-- /.container -->
    </div> <!-- /.vertical-center -->
  @stop

  @section('pageScripts')
  @stop



