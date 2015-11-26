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
          <div class="panel-body">
            <div class="row">

              @foreach (Auth::user()->dashboards as $dashboard)
                <div class="col-sm-6 col-md-4">
                  <div class="thumbnail">
                    <a href="{{ route('dashboard.dashboard', $dashboard->id) }}">
                    <img src="{{ Auth::user()->background->url }}" alt="{{ $dashboard->name }}" />
                    </a>
                    
                    <div class="modification-icons text-center drop-shadow">
                      <!-- lock dashboard icons -->
                      @if ($dashboard->is_locked)
                        <span class="lock-icon" data-toggle="tooltip" data-placement="bottom" title="This dashboard is locked. Click to unlock." data-dashboard-id="{{ $dashboard->id }}" data-lock-direction="unlock">
                          <span class="label label-danger"><i class="fa fa-lock"></i></span>
                        </span>
                      @else
                        <span class="lock-icon" data-toggle="tooltip" data-placement="bottom" title="This dashboard is unlocked. Click to lock." data-dashboard-id="{{ $dashboard->id }}" data-lock-direction="lock">
                          <span class="label label-primary"><i class="fa fa-unlock-alt"></i></span>
                        </span>
                      @endif
                      <!-- /lock dashboard icons -->
                      <!-- make default icons -->
                      @if ($dashboard->is_default)
                        <span class="make-default-icon default" data-toggle="tooltip" data-placement="bottom" title="This is your default dashboard." data-dashboard-id="{{ $dashboard->id }}">
                          <span class="label label-danger"><i class="fa fa-star"></i></span>
                        </span>
                      @else
                        <span class="make-default-icon" data-toggle="tooltip" data-placement="bottom" title="Make this the default dashboard." data-dashboard-id="{{ $dashboard->id }}">
                          <span class="label label-primary"><i class="fa fa-star"></i></span>
                        </span>
                      @endif
                      <!-- /make default icons -->  
                    </div> <!-- /.modification-icons -->
                    
                    <div class="caption text-center">
                      <h5>{{ $dashboard->name }}</h5>
                      <p>
                        <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#rename-dashboard-modal" data-dashboard-name="{{ $dashboard->name }}" data-dashboard-id="{{ $dashboard->id }}" >Edit name</button>
                        <button class="btn btn-sm btn-danger delete-dashboard" onclick="deleteDashboard({{ $dashboard->id }});">Delete</button>
                      </p>
                    </div> <!-- /.caption -->
                  </div> <!-- /.thumbnail -->
                </div> <!-- /.col-sm-6 -->
              @endforeach
              
              <div class="col-sm-6 col-md-4">
                <div class="add-new text-center no-underline clickable larger-text" onclick="$('#add-dashboard-modal').modal('show');">
                  Add new...
                </div> <!-- /.add-new -->  
              </div> <!-- /.col-sm-6 -->

              <!-- Add new dashboard modal -->
              <div class="modal fade" id="add-dashboard-modal" tabindex="-1" role="dialog" aria-labelledby="add-dashboard-label">
                <div class="modal-dialog" role="document">
                  <div class="modal-content">
                    <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                      <h4 class="modal-title" id="add-dashboard-label">Add new dashboard</h4>
                    </div>
                    <form id="add-dashboard-form" class="form-horizontal">
                      <div class="modal-body">
                          <div id="name-input-group" class="form-group">
                            <label for="new-dashboard" class="col-sm-5 control-label">Choose a name</label>
                            <div class="col-sm-7">
                              <input id="name-input" type="text" class="form-control" />
                            </div> <!-- /.col-sm-7 -->
                          </div> <!-- /.form-group -->
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add</button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
              <!-- /Add new dashboard modal -->

              <!-- Rename dashboard modal -->
              <div class="modal fade" id="rename-dashboard-modal" tabindex="-1" role="dialog" aria-labelledby="rename-dashboard-label">
                <div class="modal-dialog" role="document">
                  <div class="modal-content">
                    <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                      <h4 class="modal-title" id="rename-dashboard-label">Rename dashboard</h4>
                    </div>
                    <form id="rename-dashboard-form" class="form-horizontal">
                      <div class="modal-body">
                          <div id="rename-input-group" class="form-group">
                            <label for="new-dashboard" class="col-sm-5 control-label">Rename the dashboard</label>
                            <div class="col-sm-7">
                              <input id="rename-input" type="text" class="form-control" />
                            </div> <!-- /.col-sm-7 -->
                          </div> <!-- /.form-group -->
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Rename</button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
              <!-- /Rename dashboard modal -->
              
            </div> <!-- /.row -->
          </div> <!-- /.panel-body -->
        </div> <!-- /.panel -->
      </div> <!-- /.col-md-10 -->
    </div> <!-- /.row -->

  </div>
  @stop

  @section('pageScripts')
    @include('dashboard.manage-dashboards-makedefault-scripts')
    @include('dashboard.manage-dashboards-locking-scripts')
  
  <script type="text/javascript">

  // Delete dashboard.
  function deleteDashboard(dashboardId) {
    $.ajax({
      type: "POST",
      url: "{{ route('dashboard.delete', 'dashboard_id') }}".replace('dashboard_id', dashboardId)
     }).done(function() {
      window.location.href = '{{ URL::route('dashboard.manage') }}'
     });
  }

  // Rename dashboard.
  function renameDashboard(dashboardId, dashboardName) {
    $.ajax({
      type: "post",
      data: {'dashboard_name': dashboardName},
      url: "{{ route('dashboard.rename', 'dashboard_id') }}".replace('dashboard_id', dashboardId)
     }).done(function () {
       window.location.href = '{{ URL::route('dashboard.manage') }}'
     });
  }


  $(document).ready(function () {
    // If Add New Dashboard modal is shown, focus into input field.
    $('#add-dashboard-modal').on('shown.bs.modal', function () {
      $('#name-input').focus()
    });

    // If Add New Dashboard modal is submitted, validate data and create new dashboard then reload page.
    $('#add-dashboard-form').submit(function() {
      var newDashboardName = $('#name-input').val();

      if (newDashboardName.length > 0) {
        $.ajax({
          type: "post",
          data: {'dashboard_name': newDashboardName},
          url: "{{ route('dashboard.create') }}"
         }).done(function () {
          $('#name-input-group').removeClass('has-error');
           location.reload();
         });
        return
      } else {  
        $('#name-input-group').addClass('has-error');
        event.preventDefault();
      }
      
    });

    // If Rename Dashboard modal is shown, focus into input field and change value.
    $('#rename-dashboard-modal').on('show.bs.modal', function (event) {
      var button = $(event.relatedTarget);
      var name = button.data('dashboard-name');
      var id = button.data('dashboard-id');

      $('#rename-input').val(name);
      $('#rename-input').focus();
      $('#rename-input').data('id', id);

    });

    // If Rename Dashboard modal is submitted, validate data and rename dashboard.
    $('#rename-dashboard-form').submit(function() {
      var newDashboardName = $('#rename-input').val();
      var id = $('#rename-input').data('id');

      if (newDashboardName.length > 0) {
        $('#rename-input-group').removeClass('has-error');
        renameDashboard(id, newDashboardName);
        return
      } else {  
        $('#rename-input-group').addClass('has-error');
        event.preventDefault();
      }
      
    });

  });

  </script>
  @append

