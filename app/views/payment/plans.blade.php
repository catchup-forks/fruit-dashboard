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
                    {{ $plan->description }}
                    @if(Auth::user()->subscriptions()->first()->plan()->first()->id == $plan->id)
                      <p>You're currently on this plan :)</p>
                    @else
                      <a href="{{ route('payment.subscribe', $plan->id) }}" class="btn btn-success btn-block">Subscribe</a>
                    @endif
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
