@extends('meta.base-user')

  @section('pageTitle')
    Social connections
  @stop

  @section('pageStylesheet')
  @stop

  @section('pageContent')

  <div class="container">

    <h1 class="text-center text-white drop-shadow">
      Connect your social accounts
    </h1>

    <div class="row">
      <div class="col-md-10 col-md-offset-1">
        <div class="panel panel-default panel-transparent">
          <div class="panel-body">
            <div class="row">

              <div class="col-md-5">

                <div class="list-group margin-top-sm">

                @foreach (SiteConstants::getServicesMetaByType('social') as $service)
                    @if(Auth::user()->isServiceConnected($service['name']))
                      <a href="{{ route($service['disconnect_route']) }}" class="list-group-item clearfix changes-image" data-image="widget-{{ $service['name'] }}">
                    @else
                      <a href="{{ route($service['connect_route']) }}" class="list-group-item clearfix changes-image connect-redirect" data-image="widget-{{ $service['name'] }}">
                    @endif

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
                {{ HTML::image('img/demonstration/widget-facebook.jpg', 'Facebook', array('id' => 'img-change', 'class' => 'img-responsive img-rounded')) }}
              </div> <!-- /.col-md-7 -->
            </div> <!-- /.row -->

            <hr>

            <div class="row">
              <div class="col-md-12">
                <a href="{{ URL::route('signup-wizard.financial-connections') }}" class="btn btn-warning">Back</a>
                <a href="{{ URL::route('signup-wizard.web-analytics-connections') }}" class="btn btn-primary pull-right">Next</a>
                <a href="{{ URL::route('signup-wizard.web-analytics-connections') }}" class="btn btn-link pull-right">Skip</a>
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

      // Service redirection
      $('.connect-redirect').click(function(e) {
        var url = $(this).attr('href');
        e.preventDefault();
        bootbox.confirm({
          title: 'Fasten seatbelts, redirection ahead',
          message: 'To connect the service, we will redirect you to their site. Are you sure?',
          // On clicking OK redirect to fruit dashboard add widget page.
          callback: function(result) {
            if (result) {
              if (window!=window.top) {
                window.open(url, '_blank');
              } else {
                window.location = url;
              }
            }
          }
        });
      });

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
