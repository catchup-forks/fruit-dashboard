@extends('meta.base-user')

@section('pageContent')

<div id="content-wrapper">
    @parent

    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel-body bordered sameHeight">

            @if (isset($step) && ($step == 'choose-type'))
                <h4><i class="fa fa-link"></i>&nbsp;&nbsp;Select widget type</h4>

                {{ Form::open(
                array(
                'url'=>'connect/googlespreadsheet/set-type',
                'method' => 'post',
                )
                ) }}

                <input type="radio" name="type" value="google-spreadsheet-text-cell" checked/> Text widget, gets data from cell A2<br/>
                <input type="radio" name="type" value="google-spreadsheet-text-column-random"/> Text widget, randomly displays a cell from column A<br/>
                <input type="radio" name="type" value="google-spreadsheet-text-column"/> List widget, gets data from column A<br/>
                <input type="radio" name="type" value="google-spreadsheet-line-cell"/> Graph widget, gets data from cell A2<br/>
                <input type="radio" name="type" value="google-spreadsheet-line-column"/> Graph widget, gets dates from column A, values from column B<br/>

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


            @if (isset($step) && ($step == 'choose-spreadsheet'))

                <h4><i class="fa fa-link"></i>&nbsp;&nbsp;Select spreadsheet</h4>

                {{ Form::open(
                array(
                'url'=>'connect/googlespreadsheet/set-spreadsheet',
                'method' => 'post',
                )
                ) }}

                <div class="col-sm-8">
                  <select name="spreadsheetId" class="form-control">
                    @foreach ($spreadsheetFeed as $entry)
                    <option value="{{ basename($entry->getId()) }}">{{ $entry->getTitle() }}</option>
                    @endforeach
                  </select>
                </div>

                @if (isset($type))
                    <input type="hidden" name="type" value="{{ $type }}"/>
                @endif

                {{ Form::submit('Next >', array(
                'class' => 'btn btn-flat btn-info btn-sm pull-right'
                )) }}
                
                <a href="{{ URL::route('connect.connect')}}">
                  {{ Form::button('Cancel', array(
                  'class' => 'btn btn-warning btn-sm btn-flat pull-right cancelButton'
                  )) }}
                </a>

                {{ Form::close() }}

            @endif

            @if (isset($step) && ($step == 'choose-worksheet'))

                <h4><i class="fa fa-link"></i>&nbsp;&nbsp;Select worksheet</h4>

                {{ Form::open(
                array(
                'url'=>'connect/googlespreadsheet/set-worksheet',
                'method' => 'post',
                )
                ) }}

                <div class="col-sm-8">
                  <select name="worksheetName" class="form-control">
                    @foreach ($worksheetFeed as $entry)
                    <option value="{{ $entry->getTitle() }}">{{ $entry->getTitle() }}</option>
                    @endforeach
                  </select>
                </div>

                @if (isset($type))
                    <input type="hidden" name="type" value="{{ $type }}"/>
                @endif

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
