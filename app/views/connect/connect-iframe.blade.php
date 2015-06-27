@extends('meta.base-user')

@section('pageContent')

<div id="content-wrapper">
  @parent

      <div class="row">
        <div class="col-md-8 col-md-offset-2">
          <div class="panel-body bordered sameHeight">

            @if (!isset($step))

            <h4><i class="fa fa-link"></i>&nbsp;&nbsp;Enter the URL of the webpage</h4>

            {{ Form::open(
            array(
            'url'=>'connect/iframe/set-url',
            'method' => 'post',
            )
            ) }}

            <div class="col-sm-8">
              <input type="text" name="fullURL" style="width:100%" />
            </div>

            {{ Form::submit('Save', array(
            'class' => 'btn btn-flat btn-info btn-sm pull-right'
            )) }}

            <a href="{{ URL::route('connect.connect')}}">
                {{ Form::button('Cancel', array(
                'class' => 'btn btn-warning btn-sm btn-flat pull-right cancelButton'
                )) }}
            </a>

            {{ Form::close() }}

            @endif

          </div>
         </div>
      </div>
</div> <!-- / #content-wrapper -->

@stop
