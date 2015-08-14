@extends('meta.base-user')

  @section('pageTitle')
    Manage dashboards
  @stop

  @section('pageStylesheet')
  @stop

  @section('pageContent')

  <div class="container">

    <h1 class="text-center text-white drop-shadow">
      Manage dashboards
    </h1>

    <div class="row">
      <div class="col-md-10 col-md-offset-1">
        <div class="panel panel-default panel-transparent">
          <div class="panel-heading">
            <h3 class="panel-title">
              <span class="icon fa fa-list"></span> Dashboards
            </h3>
          </div>
          <div class="panel-body">
            <div>
            <form class="form-inline" id="add-form">
              <p class="text-info not-visible" id="loading">Adding...</p>
              <div class="form-group" id="add-group">
                <input type="text" class="form-control" id="dashboard-name" style="display:none">
                <button class="btn btn-primary" id="add-dashboard"><span class="icon fa fa-plus"> Add</button>
              </div>
            </form>
            </div>
            <hr>
            <div id="dashboards">
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  @stop

  @section('pageScripts')
  <script type="text/javascript">
    function getDashboards() {
      var selector = $("#dashboards");
      $.ajax({
        type: "POST",
        url: "{{ route('dashboard.get-dashboards') }}"
       }).done(function (dashboards) {
        selector.html('');
        for (var i = 0; i < dashboards.length; ++i) {
          var dashboard = dashboards[i];
          // Edit fields.
          var editInput = '<input id="rename-field-' +  dashboard['id'] +'" type="text" value="' + dashboard['name'] + '" class="form-control"> ';
          var editButton = '<button onClick="renameDashboard('+dashboard['id']+')" class="btn btn-sm btn-success">Rename</button>';

          var editField = '<form id="edit-form-' + dashboard['id'] + '" class="form-inline" style="display:none"><div class="form-group" id="edit-group-' + dashboard['id'] + '">' + editInput + editButton + '</div></form>';

          // Name.
          var name = '<span id="dashboard-name-' + dashboard['id'] + '">' + dashboard['name'] + '</span>';
          var nameEdit = '<input id="dashboard-edit" type="text" value="' + dashboard['name'] + '">'

          // Buttons.
          /*
          var lock   = '<button class="btn btn-sm btn-info"><span class="icon fa fa-lock"></span></button> '; */
          var rename = '<button class="btn btn-sm btn-warning" onClick="showRename(' + dashboard['id'] + ')" ><span class="icon fa fa-edit"></span></button> ';
          var remove = '<button class="btn btn-sm btn-danger" onClick="deleteDashboard(' + dashboard['id'] + ')" ><span class="icon fa fa-trash"></span></button> ';

          // Appending div.
          selector.append('<div class="row col-md-12 margin-top-sm">' + name + editField + '<span class="pull-right" id="edit-tools-' + dashboard['id'] +'">' + rename + remove + '</span>' + '</div>');
        }
       });
    }

    // Delete dashboard.
    function deleteDashboard(dashboardId) {
      $.ajax({
        type: "POST",
        url: "{{ route('dashboard.delete', 'dashboard_id') }}".replace('dashboard_id', dashboardId)
       }).done(function () {
        getDashboards();
       });
    }

    // Rename dashboard.
    function renameDashboard(dashboardId) {
      var nameElement = $("#rename-field-" + dashboardId);
      var groupElement = $("#edit-group-" + dashboardId);
      if (nameElement.val().length < 1) {
        groupElement.addClass('has-error');
        return;
      }
      $.ajax({
        type: "post",
        data: {'dashboard_name': nameElement.val()},
        url: "{{ route('dashboard.rename', 'dashboard_id') }}".replace('dashboard_id', dashboardId)
       }).done(function () {
         getDashboards();
       });
    }

    function showRename(dashboardId) {
      $("#dashboard-name-" + dashboardId).hide();
      $("#edit-tools-" + dashboardId).hide();
      $("#edit-form-" + dashboardId).show();
      $("#rename-field-" + dashboardId).focus();
    }

    // Adding a new dashboard with validation.
    function addDasboard() {
        var name_field = $("#dashboard-name");
        var group = $("#add-group");
        if (!name_field.is(':visible')) {
          name_field.show();
          name_field.focus();
          return;
        }
        if (name_field.val().length < 1) {
          group.addClass('has-error');
          name_field.focus();
          return;
        }

        var name = name_field.val();
        // Hiding elements, showing loading.
        $("#loading").show();
        name_field.hide();
        group.hide();

        $.ajax({
          type: "post",
          data: {'dashboard_name': name},
          url: "{{ route('dashboard.create') }}"
         }).done(function () {
          // showing elements, hiding loading.
           $("#loading").hide();
           group.removeClass('has-error');
           group.show();
           name_field.val('');
           getDashboards();
         });
    }

    $(document).ready(function () {
      getDashboards();

      $("#add-form").submit(function (e) {
        e.preventDefault();
        addDasboard();
      });
      $(document).on('submit', '[id^=edit-form]', function (e){
        e.preventDefault();
      })
    });


    /* Locking/unlocking feature
    function lockDashboard(dashboardId) {
      $.ajax({
        type: "POST",
        url: "{{ route('dashboard.lock', 'dashboard_id') }}".replace('dashboard_id', dashboardId)
       }).done(function () {
        getDashboards();
       });
    }

    function unlockDashboard(dashboardId) {
      $.ajax({
        type: "POST",
        url: "{{ route('dashboard.unlock', 'dashboard_id') }}".replace('dashboard_id', dashboardId)
       }).done(function () {
         getDashboards();
       });
    } */
  </script>
  @append

