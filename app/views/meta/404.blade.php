@extends('meta.base-user-signout')

  @section('pageTitle')
    404 - url not found
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
              404
            </h1>
            <p class="lead">
              The URL you requested does not exist.
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