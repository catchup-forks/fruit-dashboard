@extends('meta.base-user')

@section('pageContent')

<div id="content-wrapper">
  @parent
      <div class="row">
        <div class="col-md-10 col-md-offset-1">
          <div class="panel-body bordered sameHeight">

            @if (!isset($step))

            {{ Form::open(
            array(
            'url'=>'connect/quote/set-everything',
            'method' => 'post',
            )
            ) }}

            <h4>Type</h4>

            <input type="radio" name="type" value="quote-inspirational" checked="checked"/> Inspirational quotes<br/>
            <input type="radio" name="type" value="quote-funny"/> Funny quotes<br/>
            <input type="radio" name="type" value="quote-first-line"/> First lines of famous books<br/>

            <br/>
            <br/>

            <h4>Change frequency</h4>

            <input type="radio" name="refresh" value="daily" checked="checked"/> Daily<br/>
            <input type="radio" name="refresh" value="every-reload"/> Every reload<br/>

            <br/>
            <br/>

            <h4>Language</h4>

            <input type="radio" name="language" value="english" checked="checked"/> English<br/>
            <input type="radio" name="language" value="hungarian"/> Hungarian<br/>

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
