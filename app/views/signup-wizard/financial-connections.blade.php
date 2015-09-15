@extends('meta.base-user')

  @section('pageTitle')
    Financial connections
  @stop

  @section('pageStylesheet')
  @stop

  @section('pageContent')

  <div class="container">

    <h1 class="text-center text-white drop-shadow">
      Connect your financial accounts
    </h1>

    <div class="row">
      <div class="col-md-10 col-md-offset-1">
        <div class="panel panel-default panel-transparent">
          <div class="panel-body">
            <div class="row">

              <div class="col-md-5">

                <div class="list-group margin-top-sm">

               @foreach (SiteConstants::getFinancialServices() as $service)
                  <a href="
                    @if(Auth::user()->isServiceConnected($service['name']))
                      {{ route($service['disconnect_route']) }}
                    @else
                      {{ route($service['connect_route']) }}?createDashboard=true
                    @endif
                  " class="list-group-item clearfix changes-image" data-image="widget-{{ $service['name'] }}">
                    @if(Auth::user()->isServiceConnected($service['name']))
                        <small>
                          <span class="fa fa-circle text-success" data-toggle="tooltip" data-placement="left" title="Connection is alive."></span> </small>
                    @else
                        <small>
                          <span class="fa fa-circle text-danger" data-toggle="tooltip" data-placement="left" title="Not connected"></span>
                        </small>
                    @endif
                    {{ $service['display_name']}}
                    <span class="pull-right">
                      @if(Auth::user()->isServiceConnected($service['name']))
                        <button class="btn btn-xs btn-danger">
                          Disconnect
                        </button>
                      @else
                        <button class="btn btn-xs btn-success" >
                          Connect
                        </button>
                      @endif
                    </span>
                  </a>
                  @endforeach

                </div> <!-- /.list-group -->

              </div> <!-- /.col-md-5 -->
              <div class="col-md-7">
                {{ HTML::image('img/demonstration/widget-braintree.jpg', 'Braintree', array('id' => 'img-change', 'class' => 'img-responsive img-rounded')) }}
              </div> <!-- /.col-md-7 -->
            </div> <!-- /.row -->

            <hr>

            <div class="row">
              <div class="col-md-12">
                <a href="{{ URL::route('signup-wizard.social-connections') }}" class="btn btn-primary pull-right">Next</a>
                <a href="{{ URL::route('signup-wizard.social-connections') }}" class="btn btn-link pull-right">Skip</a>
              </div> <!-- /.col-md-12 -->
            </div> <!-- /.row -->



          </div> <!-- /.panel-body -->
        </div> <!-- /.panel -->
      </div> <!-- /.col-md-10 -->
    </div> <!-- /.row -->
  </div> <!-- /.container -->

  @stop

  @section('pageScripts')

  {{-- Change image on hover --}}
  <script type="text/javascript">
    $(function(){
      var baseUrl = "/img/demonstration/";
      var ext = ".jpg";

      $('.changes-image').hover(
        //on mouse enter
        function() {
          //rewrite img src and change alternate text
          $('#img-change').attr('src', baseUrl + $(this).data('image') + ext);
          $('#img-change').attr('alt', "The " + $(this).data('image') + " Widget.");
        });
    });
  </script>

  @stop
