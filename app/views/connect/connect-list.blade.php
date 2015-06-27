@extends('meta.base-user')

@section('pageContent')

<div id="content-wrapper">
  @parent

    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel-body bordered sameHeight">

            @if (isset($step) && ($step == 'select-source'))
                <h4><i class="fa fa-link"></i>&nbsp;&nbsp;Select list source</h4>

                {{ Form::open(
                array(
                'url'=>'connect/list/source-selected',
                'method' => 'post',
                )
                ) }}

                <input type="radio" name="source" value="google-spreadsheet-text-column" checked/> Google Spreadsheet, gets data from column A<br/>

                {{ Form::submit('Next', array(
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
