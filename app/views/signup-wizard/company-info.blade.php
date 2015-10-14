@extends('meta.base-user')

  @section('pageTitle')
    Startup information
  @stop

  @section('pageStylesheet')
  @stop

  @section('pageContent')

  <div class="container">
    <h1 class="text-center text-white drop-shadow">
      Let us know about your company
    </h1> <!-- /.text-center -->

    <div class="row">
      <div class="col-md-10 col-md-offset-1">
        <div class="panel panel-default panel-transparent margin-top">
          <div class="panel-body">

            <form method="POST" action="{{ route('signup-wizard.postStep', $currentStep) }}" class="form-horizontal">
              <div class="form-group">
                <div class="col-sm-4">
                  <label class="control-label">Name of your project</label>
                </div> <!-- /.col-sm-4 -->
                <div class="col-sm-8">
                  <input name='project_name' type='text' class='form-control' placeholder='Project name' value="{{ $info->project_name }}">
                </div> <!-- /.col-sm-8 -->
              </div> <!-- /.form-group -->

              <div class="form-group">
                <div class="col-sm-4">
                  <label class="control-label">Url of your project</label>
                </div> <!-- /.col-sm-4 -->
                <div class="col-sm-8">
                  <input name='project_url' type='text' class='form-control' placeholder='http://yourproject.com' value="{{ $info->project_url }}">
                </div> <!-- /.col-sm-8 -->
              </div> <!-- /.form-group -->

              <div class="form-group">
                <div class="col-sm-4">
                  <label class="control-label">Type of your startup</label>
                </div> <!-- /.col-sm-4 -->
                <div class="col-sm-8">
                  <select name="startup_type" class="form-control">
                    <option value=''>Please select one of the following</option>
                    @foreach (SiteConstants::getSignupWizardStartupTypes() as $value => $text)
                      <option value="{{ $value }}" @if($info->startup_type == $value) selected @endif>{{ $text }}</option>
                    @endforeach
                  </select>
                </div> <!-- /.col-sm-8 -->
              </div> <!-- /.form-group -->

              <div class="form-group">
                <div class="col-sm-4">
                  <label class="control-label">Size of your startup</label>
                </div> <!-- /.col-sm-4 -->
                <div class="col-sm-8">
                  <select name="company_size" class="form-control">
                    <option value=''>Please select one of the following</option>
                    @foreach (SiteConstants::getSignupWizardCompanySize() as $value => $text)
                      <option value="{{ $value }}" @if($info->company_size == $value) selected @endif>{{ $text }}</option>
                    @endforeach
                  </select>
                </div> <!-- /.col-sm-6 -->
              </div> <!-- /.form-group -->

              <div class="form-group">
                <div class="col-sm-4">
                  <label class="control-label">Funding of your startup</label>
                </div> <!-- /.col-sm-4 -->
                <div class="col-sm-8">
                  <select name="company_funding" class="form-control">
                    <option value=''>Please select one of the following</option>
                    @foreach (SiteConstants::getSignupWizardCompanyFunding() as $value => $text)
                      <option value="{{ $value }}"@if($info->company_funding == $value) selected @endif>{{ $text }}</option>
                    @endforeach
                  </select>
                </div> <!-- /.col-sm-8 -->
              </div> <!-- /.form-group -->
             
              <div class="row">
                <div class="col-md-12">
                  <button type="submit" class='btn btn-primary pull-right'>Next</button>
                  <a href="{{ route('signup-wizard.getStep', SiteConstants::getSignupWizardStep('next', $currentStep)) }}" class="btn btn-link pull-right">Skip</a>
                </div> <!-- /.col-md-12 -->
              </div> <!-- /.row -->


            </form>

          </div> <!-- /.panel-body -->
        </div> <!-- /.panel -->
      </div> <!-- /.col-md-10 -->
    </div> <!-- /.row -->
  </div> <!-- /.container -->

  @stop

  @section('pageScripts')
  @stop
