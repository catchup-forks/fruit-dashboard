@extends('meta.base-user')

  @section('pageTitle')
    Trophies
  @stop

  @section('pageStylesheet')
  @stop

  @section('pageContent')
  <div class="container">

    <h1 class="text-center text-white drop-shadow">
      Trophies
    </h1>

    <div class="row">
      <div class="col-xs-12">
        <div class="panel panel-default panel-transparent">
          <div class="panel-body">
            <div class="row">
              <h3 class="text-left">Facebook</h3>
              <div class="col-xs-1">
                <i class="fa fa-facebook-official fa-3x"></i>
              </div> <!-- /.col-xs-1 -->
              <div class="col-xs-2">
                <div class="thumbnail">
                  <span class="fa-stack fa-1x">
                    <i class="fa fa-circle fa-stack-2x"></i>
                    <i class="fa fa-trophy fa-stack-1x fa-inverse"></i>                   
                  </span> <!-- /.fa-stack -->
                  <div class="caption">
                    <h3 class="text-center">1+</h3>
                  </div> <!-- /.caption -->
                </div> <!-- /.thumbnail -->
              </div> <!-- /.col-xs-2 -->
              <div class="col-xs-2">
                <div class="thumbnail">
                  <span class="fa-stack fa-2x">
                    <i class="fa fa-circle fa-stack-2x"></i>
                    <i class="fa fa-trophy fa-stack-1x fa-inverse"></i>                   
                  </span> <!-- /.fa-stack -->
                  <div class="caption">
                    <h3 class="text-center">10+</h3>
                  </div> <!-- /.caption -->
                </div> <!-- /.thumbnail -->
              </div> <!-- /.col-xs-2 -->
              <div class="col-xs-2">
                <div class="thumbnail">
                  <span class="fa-stack fa-3x">
                    <i class="fa fa-circle fa-stack-2x"></i>
                    <i class="fa fa-trophy fa-stack-1x fa-inverse"></i>                   
                  </span> <!-- /.fa-stack -->
                  <div class="caption">
                    <h3 class="text-center">100+</h3>
                  </div> <!-- /.caption -->
                </div> <!-- /.thumbnail -->
              </div> <!-- /.col-xs-2 -->
              <div class="col-xs-2">
                <div class="thumbnail">
                  <span class="fa-stack fa-4x">
                    <i class="fa fa-circle fa-stack-2x"></i>
                    <i class="fa fa-trophy fa-stack-1x fa-inverse"></i>                   
                  </span> <!-- /.fa-stack -->
                  <div class="caption">
                    <h3 class="text-center">1000+</h3>
                  </div> <!-- /.caption -->
                </div> <!-- /.thumbnail -->
              </div> <!-- /.col-xs-2 -->
              <div class="col-xs-2">
                <div class="thumbnail">
                  <span class="fa-stack fa-5x">
                    <i class="fa fa-circle fa-stack-2x"></i>
                    <i class="fa fa-trophy fa-stack-1x fa-inverse"></i>                   
                  </span> <!-- /.fa-stack -->
                  <div class="caption">
                    <h3 class="text-center">10000+</h3>
                  </div> <!-- /.caption -->
                </div> <!-- /.thumbnail -->
              </div> <!-- /.col-xs-2 -->
            </div> <!-- /.row -->
          </div> <!-- /.panel-body -->
        </div> <!-- /.panel -->
      </div> <!-- /.col-xs-12 -->
    </div> <!-- /.row -->
  </div> <!-- /.container -->


  @stop