@extends('meta.base-user')

  @section('pageTitle')
    Company information
  @stop

  @section('pageStylesheet')
  @stop

  @section('pageContent')

  <div class="container">
    <div class="row not-visible margin-top">
      <div class="col-md-10 col-md-offset-1">
        <div class="panel panel-default panel-transparent">
          <div class="panel-body">
            <span class="pull-right" title="We need these so that your dashboard will be more tailor-made for your needs." data-toggle="tooltip" data-placement="bottom">
              <sup>
                <i class="fa fa-2x fa-info-circle text-muted"></i>
              </sup>
            </span>
            <h1 class="text-center">
              Tell us about your project
            </h1> <!-- /.text-center -->

            <div class="row margin-top">
              <div class="col-md-12">
                
                <form method="POST" action="{{ route('signup-wizard.postStep', $currentStep) }}" class="form-horizontal">
                  
                  <div class="form-group">
                      <label class="col-sm-3 control-label">Project name</label>
                    <div class="col-sm-9">
                      <input name='project_name' type='text' class='form-control' placeholder='Project name' value="{{ $info->project_name }}">
                    </div> <!-- /.col-sm-9 -->
                  </div> <!-- /.form-group -->

                  <div class="form-group">
                    <label class="col-sm-3 control-label">Webpage</label>
                    <div class="col-sm-9">
                      <input name='project_url' type='text' class='form-control' placeholder='http://yourproject.com' value="{{ $info->project_url }}">
                    </div> <!-- /.col-sm-9 -->
                  </div> <!-- /.form-group -->

                  <div class="form-group">
                    <label class="col-sm-3 control-label">Startup type</label>
                    <div class="col-sm-9">
                      <select name="startup_type" class="form-control">
                        <option value=''>Please select one of the following</option>
                        @foreach (SiteConstants::getSignupWizardStartupTypes() as $value => $text)
                          <option value="{{ $value }}" @if($info->startup_type == $value) selected @endif>{{ $text }}</option>
                        @endforeach
                      </select>
                    </div> <!-- /.col-sm-9 -->
                  </div> <!-- /.form-group -->

                  <div class="form-group">
                    <label class="col-sm-3 control-label">Team members</label>
                    <div class="col-sm-9">
                      <select name="company_size" class="form-control">
                        <option value=''>Please select one of the following</option>
                        @foreach (SiteConstants::getSignupWizardCompanySize() as $value => $text)
                          <option value="{{ $value }}" @if($info->company_size == $value) selected @endif>{{ $text }}</option>
                        @endforeach
                      </select>
                    </div> <!-- /.col-sm-9 -->
                  </div> <!-- /.form-group -->

                  <div class="form-group">
                    <label class="col-sm-3 control-label">Funding</label>
                    <div class="col-sm-9">
                      <select name="company_funding" class="form-control">
                        <option value=''>Please select one of the following</option>
                        @foreach (SiteConstants::getSignupWizardCompanyFunding() as $value => $text)
                          <option value="{{ $value }}"@if($info->company_funding == $value) selected @endif>{{ $text }}</option>
                        @endforeach
                      </select>
                    </div> <!-- /.col-sm-9 -->
                  </div> <!-- /.form-group -->

                  <hr />
                 
                  <div class="row">
                    <div class="col-md-12">
                      <button type="submit" class='btn btn-primary pull-right'>Next</button>
                      <a href="{{ route('signup-wizard.getStep', SiteConstants::getSignupWizardStep('next', $currentStep)) }}" class="btn btn-link pull-right">Skip</a>
                    </div> <!-- /.col-md-12 -->
                  </div> <!-- /.row -->

                </form>
              </div> <!-- /.col-md-12 -->
            </div> <!-- /.row -->
          </div> <!-- /.panel-body -->
        </div> <!-- /.panel -->
      </div> <!-- /.col-md-10 -->
    </div> <!-- /.row -->
  </div> <!-- /.container -->

  @stop

  @section('pageScripts')
    <script type="text/javascript">
      $(function(){

        setTimeout(function(){
          $('.not-visible').fadeIn();
        }, 1000);

      })
    </script>
  @stop
