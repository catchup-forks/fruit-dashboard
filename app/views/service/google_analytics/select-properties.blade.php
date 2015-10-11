@extends('meta.base-user')

  @section('pageTitle')
    Select profiles & goals
  @stop

  @section('pageStylesheet')
  @stop

  @section('pageContent')

  <div class="container">
    <h1 class="text-center text-white drop-shadow">
      Select your Google Analytics profile
    </h1> <!-- /.text-center -->

    <div class="row">
      <div class="col-md-10 col-md-offset-1">
        <div class="panel panel-default panel-transparent margin-top">
          <div class="panel-body">

            {{ Form::open(array(
              'route' => array('service.google_analytics.select-properties'))) }}

            <div class="form-group">

              <div class="row">

                <div class="col-sm-6">

                  {{ Form::label('properties', 'Google Analytics profile', array(
                    'class' => 'control-label'
                  ))}}

                  {{ Form::select('profiles[]', $profiles, null, array(
                      'class' => 'form-control', 'size' => 15, 'id' => 'profile-select'
                    ))}}
                </div>

                <div class="col-sm-6">
                  <div id="goal-onload">
                    <h3> Please select a profile first. </h3>
                  </div>

                  <div class="not-visible text-center" id="goal-load">
                    <i class="fa fa-3x fa-circle-o-notch fa-spin"></i>
                    <h4>Loading...</h4>
                  </div>

                  <div class="alert alert-warning not-visible" id="goal-not-found">
                    <strong>
                      <span class="fa fa-exclamation-triangle"></span>
                    </strong>
                        You don't have any goals associated with this profile. <br>
                        You can still continue, but won't get full experience.
                  </div>

                  <div class="not-visible" id="goals">
                    {{ Form::label('goals', 'Google Analytics goal', array(
                      'class' => 'control-label'
                    ))}}

                    {{ Form::select('goals[]', array(), null, array(
                        'class' => 'form-control', 'size' => 15, 'id' => 'goal-select'
                      ))}}
                  </div>
                </div>

              </div> <!-- /.row -->

              <div class="row">

                <div class="col-md-12">

                  <hr>

                  <a href="{{ route('signup-wizard.social-connections') }}" class="btn btn-warning">Cancel</a>

                  {{ Form::submit('Select', array(
                    'class' => 'btn btn-primary pull-right'
                  )) }}

                </div> <!-- /.col-md-12 -->

              </div> <!-- /.row -->

            </div> <!-- /.form-group -->

            {{ Form::close() }}

          </div> <!-- /.panel-body -->
        </div> <!-- /.panel -->
      </div> <!-- /.col-md-10 -->
    </div> <!-- /.row -->
  </div> <!-- /.container -->

  @stop

@section('pageScripts')
  <script type="text/javascript">
  $(document).ready(function () {
    function load() {
      $("#goal-onload").hide();
      $("#goal-not-found").hide();
      $("#goals").hide();
      $("#goal-load").show();
    }
    $("#profile-select").change(function () {
      /* changed */
      var goals_select = $("#goal-select");
      var profile_id = $("#profile-select").val();
      load();
      $.ajax({
        type: "get",
        url: "{{ route('service.google_analytics.get-goals', 'profile_id') }}".replace("profile_id", profile_id),
       }).done(function (data) {
        /* AJAX ready */
        $("#goal-load").hide();
        goals_select.empty();
        if (data == false) {
          $("#goal-not-found").show();
        } else {
          $.each(data, function(id, name) {
            goals_select.append($("<option></option>")
              .attr('value', id).text(name));
          });
          $("#goals").show();
        }

       });
    });
  });
  </script>
@append
