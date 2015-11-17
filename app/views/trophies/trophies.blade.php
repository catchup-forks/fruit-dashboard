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
          <div class="panel-heading">
            <h3 class="panel-title">
              <i class="fa fa-facebook-square"></i>
              <strong>Faceboook</strong>
            </h3> <!-- /.panel-title -->
          </div> <!-- /.panel-heading -->
          <div class="panel-body">
            <div class="row">
              <div class="col-xs-1"></div>
              @foreach(array(0,1,2,3,4) as $value)
                <div class="col-xs-2">
                  <div class="thumbnail" style="margin-top: {{ (4 - $value)*10 }}%; background-color: rgba(0,{{ 172 + ($value*6) }},0, {{ 0.45 + ($value/10) }})">
                    <span class="fa-stack fa-{{ $value+1 }}x trophy">
                      <i class="fa fa-circle fa-stack-2x" style="color: rgb(0,128,0)"></i>
                      <i class="fa fa-trophy fa-stack-1x fa-inverse"></i>                   
                    </span> <!-- /.fa-stack -->
                    <div class="caption">
                      <h4 class="text-center">
                        <i class="fa fa-facebook-square"></i>
                        <strong>{{ pow(10, $value) }}+</strong>
                      </h4>
                    </div> <!-- /.caption -->
                  </div> <!-- /.thumbnail -->
                </div> <!-- /.col-xs-2 -->
              @endforeach
            </div> <!-- /.row -->
<<<<<<< HEAD
=======
            <div class="row">
              <div class="col-xs-1"></div>
              @foreach(array(0,1,2,3,4) as $value)
                <div class="col-xs-2 text-center">
                  <a href="#">How to reach this?</a>
                </div> <!-- /.col-xs-2 -->
              @endforeach
            </div> <!-- /.row -->
>>>>>>> 5852714487ae7829e17b338c840b097d2f8053d0
          </div> <!-- /.panel-body -->
        </div> <!-- /.panel -->
      </div> <!-- /.col-xs-12 -->
    </div> <!-- /.row -->
    <div class="row">
      <div class="col-xs-12">
        <div class="panel panel-default panel-transparent">
          <div class="panel-heading">
            <h3 class="panel-title">
              <i class="fa fa-twitter"></i>
              <strong>Twitter</strong>
            </h3> <!-- /.panel-title -->
          </div> <!-- /.panel-heading -->
          <div class="panel-body">
            <div class="row">
              <div class="col-xs-1"></div>
              @foreach(array(0,1,2,3,4) as $value)
                <div class="col-xs-2">
                  <div class="thumbnail" style="margin-top: {{ (4 - $value)*10 }}%">
                    <span class="fa-stack fa-{{ $value+1 }}x trophy">
                      <i class="fa fa-circle fa-stack-2x"></i>
                      <i class="fa fa-trophy fa-stack-1x fa-inverse"></i>                   
                    </span> <!-- /.fa-stack -->
                    <div class="caption">
                      <h4 class="text-center">
                        <i class="fa fa-twitter"></i>
                        <strong>{{ pow(10, $value) }}+</strong>
                      </h4>
                    </div> <!-- /.caption -->
                  </div> <!-- /.thumbnail -->
                </div> <!-- /.col-xs-2 -->
              @endforeach
            </div> <!-- /.row -->
<<<<<<< HEAD
=======
            <div class="row">
              <div class="col-xs-1"></div>
              @foreach(array(0,1,2,3,4) as $value)
                <div class="col-xs-2 text-center">
                  <a href="#">How to reach this?</a>
                </div> <!-- /.col-xs-2 -->
              @endforeach
            </div> <!-- /.row -->
>>>>>>> 5852714487ae7829e17b338c840b097d2f8053d0
          </div> <!-- /.panel-body -->
        </div> <!-- /.panel -->
      </div> <!-- /.col-xs-12 -->
    </div> <!-- /.row -->
    <div class="row">
      <div class="col-xs-12">
        <div class="panel panel-default panel-transparent">
          <div class="panel-heading">
            <h3 class="panel-title">
              <i class="fa fa-users"></i>
              <strong>Users</strong>
            </h3> <!-- /.panel-title -->
          </div> <!-- /.panel-heading -->
          <div class="panel-body">
            <div class="row">
              <div class="col-xs-1"></div>
              @foreach(array(0,1,2,3,4) as $value)
                <div class="col-xs-2">
                  <div class="thumbnail" style="margin-top: {{ (4 - $value)*10 }}%">
                    <span class="fa-stack fa-{{ $value+1 }}x trophy">
                      <i class="fa fa-circle fa-stack-2x"></i>
                      <i class="fa fa-trophy fa-stack-1x fa-inverse"></i>                   
                    </span> <!-- /.fa-stack -->
                    <div class="caption">
                      <h4 class="text-center">
                        <i class="fa fa-users"></i>
                        <strong>{{ pow(10, $value) }}+</strong>
                      </h4>
                    </div> <!-- /.caption -->
                  </div> <!-- /.thumbnail -->
                </div> <!-- /.col-xs-2 -->
              @endforeach
            </div> <!-- /.row -->
<<<<<<< HEAD
=======
            <div class="row">
              <div class="col-xs-1"></div>
              @foreach(array(0,1,2,3,4) as $value)
                <div class="col-xs-2 text-center">
                  <a href="#">How to reach this?</a>
                </div> <!-- /.col-xs-2 -->
              @endforeach
            </div> <!-- /.row -->
>>>>>>> 5852714487ae7829e17b338c840b097d2f8053d0
          </div> <!-- /.panel-body -->
        </div> <!-- /.panel -->
      </div> <!-- /.col-xs-12 -->
    </div> <!-- /.row -->
    <div class="row">
      <div class="col-xs-12">
        <div class="panel panel-default panel-transparent">
          <div class="panel-heading">
            <h3 class="panel-title">
              <i class="fa fa-user"></i>
              <strong>Web Unique Visitor</strong>
            </h3> <!-- /.panel-title -->
          </div> <!-- /.panel-heading -->
          <div class="panel-body">
            <div class="row">
              <div class="col-xs-1"></div>
              @foreach(array(0,1,2,3,4) as $value)
                <div class="col-xs-2">
                  <div class="thumbnail" style="margin-top: {{ (4 - $value)*10 }}%;">
                    <span class="fa-stack fa-{{ $value+1 }}x trophy">
                      <i class="fa fa-circle fa-stack-2x"></i>
                      <i class="fa fa-trophy fa-stack-1x fa-inverse"></i>                   
                    </span> <!-- /.fa-stack -->
                    <div class="caption">
                      <h4 class="text-center">
                        <i class="fa fa-user"></i>
                        <strong>{{ pow(10, $value) }}+</strong>
                      </h4>
                    </div> <!-- /.caption -->
                  </div> <!-- /.thumbnail -->
                </div> <!-- /.col-xs-2 -->
              @endforeach
            </div> <!-- /.row -->
<<<<<<< HEAD
=======
            <div class="row">
              <div class="col-xs-1"></div>
              @foreach(array(0,1,2,3,4) as $value)
                <div class="col-xs-2 text-center">
                  <a href="#">How to reach this?</a>
                </div> <!-- /.col-xs-2 -->
              @endforeach
            </div> <!-- /.row -->
>>>>>>> 5852714487ae7829e17b338c840b097d2f8053d0
          </div> <!-- /.panel-body -->
        </div> <!-- /.panel -->
      </div> <!-- /.col-xs-12 -->
    </div> <!-- /.row -->
    <div class="row">
      <div class="col-xs-12">
        <div class="panel panel-default panel-transparent">
          <div class="panel-heading">
            <h3 class="panel-title">
              <i class="fa fa-dollar"></i>
              <strong>Paying user</strong>
            </h3> <!-- /.panel-title -->
          </div> <!-- /.panel-heading -->
          <div class="panel-body">
            <div class="row">
              <div class="col-xs-1"></div>
              @foreach(array(0,1,2,3,4) as $value)
                <div class="col-xs-2">
                  <div class="thumbnail" style="margin-top: {{ (4 - $value)*10 }}%">
                    <span class="fa-stack fa-{{ $value+1 }}x trophy">
                      <i class="fa fa-circle fa-stack-2x"></i>
                      <i class="fa fa-trophy fa-stack-1x fa-inverse"></i>                   
                    </span> <!-- /.fa-stack -->
                    <div class="caption">
                      <h4 class="text-center">
                        <i class="fa fa-dollar"></i>
                        <strong>{{ pow(10, $value) }}+</strong>
                      </h4>
                    </div> <!-- /.caption -->
                  </div> <!-- /.thumbnail -->
                </div> <!-- /.col-xs-2 -->
              @endforeach
            </div> <!-- /.row -->
<<<<<<< HEAD
=======
            <div class="row">
              <div class="col-xs-1"></div>
              @foreach(array(0,1,2,3,4) as $value)
                <div class="col-xs-2 text-center">
                  <a href="#">How to reach this?</a>
                </div> <!-- /.col-xs-2 -->
              @endforeach
            </div> <!-- /.row -->
>>>>>>> 5852714487ae7829e17b338c840b097d2f8053d0
          </div> <!-- /.panel-body -->
        </div> <!-- /.panel -->
      </div> <!-- /.col-xs-12 -->
    </div> <!-- /.row -->
    <div class="row">
      <div class="col-xs-12">
        <div class="panel panel-default panel-transparent">
          <div class="panel-heading">
            <h3 class="panel-title">
              <strong>Product usage</strong>
            </h3> <!-- /.panel-title -->
          </div> <!-- /.panel-heading -->
          <div class="panel-body">
            <div class="row">
              <div class="col-xs-4">
<<<<<<< HEAD
                <div class="thumbnail">
=======
                <div class="thumbnail" style="padding-bottom: 10%">
>>>>>>> 5852714487ae7829e17b338c840b097d2f8053d0
                  <span class="fa-stack fa-5x trophy">
                    <i class="fa fa-circle fa-stack-2x"></i>
                    <i class="fa fa-trophy fa-stack-1x fa-inverse"></i>                   
                  </span> <!-- /.fa-stack -->
                  <div class="caption">
                    <h4 class="text-center">
                      <strong>Full stack</strong><br></br>
<<<<<<< HEAD
                      <span>Added every tool [1 financial, 1 user count, 1 web analytics, 1 social]</span>
=======
                      <span>Added every tool</span>
>>>>>>> 5852714487ae7829e17b338c840b097d2f8053d0
                    </h4>
                  </div> <!-- /.caption -->
                </div> <!-- /.thumbnail -->
              </div> <!-- /.col-xs-4 -->
              <div class="col-xs-4">
<<<<<<< HEAD
                <div class="thumbnail">
=======
                <div class="thumbnail" style="padding-bottom: 10%">
>>>>>>> 5852714487ae7829e17b338c840b097d2f8053d0
                  <span class="fa-stack fa-5x trophy">
                    <i class="fa fa-circle fa-stack-2x"></i>
                    <i class="fa fa-trophy fa-stack-1x fa-inverse"></i>                   
                  </span> <!-- /.fa-stack -->
                  <div class="caption">
                    <h4 class="text-center">
                      <strong>Communicator</strong><br></br>
                      <span>Shared a widget</span>
                    </h4>
                  </div> <!-- /.caption -->
                </div> <!-- /.thumbnail -->
              </div> <!-- /.col-xs-4 -->  
              <div class="col-xs-4">
<<<<<<< HEAD
                <div class="thumbnail">
=======
                <div class="thumbnail" style="padding-bottom: 5%">
>>>>>>> 5852714487ae7829e17b338c840b097d2f8053d0
                  <span class="fa-stack fa-5x trophy">
                    <i class="fa fa-circle fa-stack-2x"></i>
                    <i class="fa fa-trophy fa-stack-1x fa-inverse"></i>                   
                  </span> <!-- /.fa-stack -->
                  <div class="caption">
                    <h4 class="text-center">
                      <strong>Community</strong><br></br>
                      <span>Joined & commented at the community</span>
                    </h4>
                  </div> <!-- /.caption -->
                </div> <!-- /.thumbnail -->
              </div> <!-- /.col-xs-4 -->
<<<<<<< HEAD
=======
            </div> <!-- /.row -->
            <div class="row">
              @foreach(array(0,1,2) as $value)
                <div class="col-xs-4 text-center">
                  <a href="#">How to reach this?</a>
                </div> <!-- /.col-xs-2 -->
              @endforeach
>>>>>>> 5852714487ae7829e17b338c840b097d2f8053d0
            </div> <!-- /.row -->
          </div> <!-- /.panel-body -->
        </div> <!-- /.panel -->
      </div> <!-- /.col-xs-12 -->
    </div> <!-- /.row -->
  </div> <!-- /.container -->


  @stop