@extends('meta.base-user')

  @section('pageTitle')
    Select page
  @stop

  @section('pageStylesheet')
  @stop

  @section('pageContent')

  <div class="container">
    <div class="row not-visible margin-top">
      <div class="col-md-10 col-md-offset-1">
        <div class="panel panel-default panel-transparent">
          <div class="panel-body">
            <span class="pull-right" title="Select the most important page now. You will be able to add more later on." data-toggle="tooltip" data-placement="bottom">
              <sup>
                <i class="fa fa-2x fa-info-circle text-muted"></i>
              </sup>
            </span>

            <h1 class="text-center">
              Select a Facebook page to analyze
            </h1> <!-- /.text-center -->

            <div class="row margin-top">
              <div class="col-md-12">
                  {{ Form::open(array(
                    'route' => array('service.facebook.select-pages'),
                    'class' => 'form-horizontal'
                    )) }}

                  <div class="form-group">


                      {{ Form::label('pages', 'Facebook page', array(
                        'class' => 'col-sm-3 control-label'
                      ))}}

                      <div class="col-sm-6">
                        
                        {{ Form::select('pages[]', $pages, null, array(
                          'class' => 'form-control'
                        ))}}

                      </div> <!-- /.col-sm-6 -->

                    </div> <!-- /.form-group -->

                    <hr />

                    <div class="row">
                      
                      <div class="col-md-12">


                        <a href="{{ $cancelRoute }}" class="btn btn-warning">Cancel</a>

                        {{ Form::submit('Select', array(
                          'class' => 'btn btn-primary pull-right'
                        )) }}
                
                      </div> <!-- /.col-md-12 -->
                      
                    </div> <!-- /.row -->

                {{ Form::close() }}
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
