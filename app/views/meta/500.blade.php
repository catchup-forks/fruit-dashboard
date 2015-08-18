@extends('meta.base-user-signout')

  @section('pageTitle')
    500 - server error
  @stop

@section('pageContent')

<body style="background: url({{ Background::getBaseUrl() }}) no-repeat center center fixed">

<div class="vertical-center">
  <div class="container">

    <div class="row">
      <div class="col-md-6 col-md-offset-3">
        <div class="panel panel-default panel-transparent">
          <div class="panel-body text-center">
            <h1>
              500
            </h1>
            <p class="lead">
              Somehow the server broke. Don't worry, we have been notified.
            </p>  
            <a href="/">Take me to a safe place.</a>
          </div> <!-- /.panel-body -->
        </div> <!-- /.panel -->
      </div> <!-- /.col-md-6 -->
    </div> <!-- /.row -->

  </div> <!-- /.container -->
</div> <!-- /.vertical-center -->

</body>

@stop

@section('pageScripts')

@stop