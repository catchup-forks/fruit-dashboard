@extends('meta.base-user')

  @section('pageContent')

    
      <div class='page-pricing'>
        @parent
        <div id="plansContainer" class="container">
          <h3 class="containerHeader">Plans</h1>
          <div class="row">
            <!-- Free Plan -->
            <div id="freePlan" class='plan-col col-xs-3 col-xs-offset-3'>
              <div class='plan-header bg-light-green darker'>
                <h4>Free</h4>
                <span>{{$plans[0]->description}}</span>
              </div>
              <div class='plan-pricing bg-light-green darken'>
                <span class='plan-currency'>{{ Config::get('constants')[strtolower($plans[0]->currencyIsoCode)] }}</span>
                <span class='plan-value'>{{ round($plans[0]->price) }}</span>
                <span class='plan-period'>/MO</span>
              </div>
              <ul class='plan-features'>
                <li>Create graphs</li>
                <li>Up to 3 service connections</li>
                <li>Premium support</li>
                <a href='/plans/{{snake_case(camel_case($plans[0]->name))}}' class='bg-light-green darker'><h4>SIGN UP</h4></a>
              </ul>
            </div>
            <!-- Free Plan -->

            <!-- Premium Plan -->
            <div id="premiumPlan" class='plan-col col-xs-3'>
              <div class='plan-header bg-light-green darker'>
                <h4>Premium</h4>
                <span>{{$plans[0]->description}}</span>
              </div>
              <div class='plan-pricing bg-light-green darken'>
                <span class='plan-currency'>{{ Config::get('constants')[strtolower($plans[0]->currencyIsoCode)] }}</span>
                <span class='plan-value'>{{ round($plans[0]->price) }}</span>
                <span class='plan-period'>/MO</span>
              </div>
              <ul class='plan-features'>
                <li>Create graphs</li>
                <li>Up to 3 service connections</li>
                <li>Premium support</li>
                <a href='/plans/{{snake_case(camel_case($plans[0]->name))}}' class='bg-light-green darker'><h4>SIGN UP</h4></a>
              </ul>
            </div>
          </div>      <!-- row -->
          <h4 class="containerHeader">Github</h1>
        </div>      <!-- plansContainer -->
      </div>      <!-- page-pricing -->
        @stop

  @section('pageScripts')

  @stop