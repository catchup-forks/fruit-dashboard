@extends('meta.base-user')

  @section('pageTitle')
    Dashboard
  @stop

  @section('pageStylesheet')
  @stop

  @section('pageContent')
  <div class="container">
    <h1 class="text-center text-white drop-shadow">Add widgets to your dashboard</h1>
    <div class="row">
      <div class="col-md-10 col-md-offset-1">
        <div class="panel panel-default panel-transparent">
          <div class="panel-body">
            <div class="row">
              <div class="col-md-5">
                <input type="text" id="filter" autofocus="autofocus">
                <ul id="widgets-list">
                @foreach($widgetDescriptors as $descriptor)
                  <a href="{{ URL::route('widget.doAdd', array($descriptor->id)) }}"><li id="descriptor-{{ $descriptor->id }}">{{ $descriptor->name }}</li></a>
                @endforeach
                </ul>
              </div>
              <div class="col-md-7">
                <h3 id="descriptor-name"></h3>
                <div class="row">
                {{ HTML::image('img/demonstration/widget-empty.png', 'The Clock Widget', array('id' => 'img-change', 'class' => 'img-responsive img-rounded pull-right')) }}
                </div>
                <p id="descriptor-description"> </p>
                <a id="descriptor-add-link" href="#"><button class="btn btn-primary pull-right">Add</button></a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  @stop
  @section('pageScripts')

  <script type="text/javascript">
    $(document).ready(function () {

      function getID(element) {
        return element.id.substr(element.id.indexOf('-') + 1)
      }
      function showDescription(descriptorID) {
        var baseUrl = "/img/demonstration/";
        var ext = ".png";

         $.ajax({
           type: "POST",
           data: {'descriptorID': descriptorID},
           url: "{{ route('widget.get-descriptor') }}"
          }).done(function( data ) {
            $("#descriptor-name").html(data['name']);
            $("#descriptor-description").html(data['description']);
            $("#descriptor-add-link").attr("href", "{{ URL::route('widget.doAdd', 'descriptor_id') }}".replace("descriptor_id", descriptorID));
            $('#img-change').attr('src', baseUrl + 'widget-' + data['type'] + ext);
            $('#img-change').attr('alt', "The " + data['name']);
          });
      }
      $("#filter").keyup(function () {
        var filter = $(this).val();
        var count = 0;
        var last = null;
        $("#widgets-list li").each(function () {
          if ($(this).text().search(new RegExp(filter, "i")) < 0) {
            $(this).fadeOut();
          } else {
            count++;
            last = this;
            $(this).show();
          }
        });
        if (count == 1) {
          // Only one widget filtered show description.
          showDescription(getID(last));
        }

      });
    });
  </script>
  @append
