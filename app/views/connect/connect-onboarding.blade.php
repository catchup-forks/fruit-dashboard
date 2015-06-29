@extends('meta.base-user')

@section('pageContent')

<div id="content-wrapper">
    @parent

    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel-body bordered sameHeight">

            @if (isset($step) && ($step == 'show-greeting'))

                {{ Form::open(
                array(
                'url'=>'connect/onboarding/save-user',
                'method' => 'post',
                )
                ) }}

                <input type="hidden" name="tesztname" value="tesztvalue"/>

                {{ Form::submit('Next', array(
                'class' => 'btn btn-flat btn-info btn-sm pull-right'
                )) }}
                
                {{ Form::close() }}

            @endif

            @if (isset($step) && ($step == 'show-personal-widgets-wizard'))

                {{ Form::open(
                array(
                'url'=>'connect/onboarding/save-personal-widgets',
                'method' => 'post',
                )
                ) }}

                <input type="hidden" name="tesztname2" value="tesztvalue2"/>
                
                {{ Form::submit('Save', array(
                'class' => 'btn btn-flat btn-info btn-sm pull-right'
                )) }}
                
                <a href="/connect/onboarding/save-personal-widgets">
                  {{ Form::button('Skip', array(
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
