@extends('meta.base-user')

  @section('pageTitle')
    Plans and Pricing
  @stop

  @section('pageStylesheet')
  @stop

  @section('pageContent')
    <div class="vertical-center">
        <div class="container">           
          <div class="row">

            @foreach ($plans as $plan)
              <div class="col-md-4">
                <div class="panel panel-default panel-transparent">
                  <div class="panel-body text-center">
                    <h1>{{ $plan->name }}</h1>
                    <p class="lead">
                      <span class="fa fa-eur"></span> 
                      {{ $plan->amount }}
                    </p>
                    <ul class="list-group">
                      <li class="list-group-item">Cras justo odio</li>
                      <li class="list-group-item">Dapibus ac facilisis in</li>
                      <li class="list-group-item">Morbi leo risus</li>
                      <li class="list-group-item">Porta ac consectetur ac</li>
                      <li class="list-group-item">Vestibulum at eros</li>
                    </ul>
                    <p><small>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Voluptatum cum suscipit voluptate sint sapiente sunt vero facere error, in, delectus doloribus ducimus earum tenetur nostrum quaerat minima pariatur distinctio labore adipisci similique dolores. Ullam velit consequatur, deleniti officiis perspiciatis quos architecto natus. Dolores doloribus perspiciatis explicabo, facilis quae delectus aperiam!</small></p>
                    
                    <a href="#" class="btn btn-success btn-block">Click me</a>
                  </div> <!-- /.panel-body -->
                </div> <!-- /.panel -->
              </div> <!-- /.col-md-4 -->
            @endforeach
            
          </div> <!-- /.row -->
        </div> <!-- /.container -->
    </div> <!-- /.vertical-center -->

    

  @stop

  @section('pageScripts')
  @stop
