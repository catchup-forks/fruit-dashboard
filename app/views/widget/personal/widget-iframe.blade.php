<iframe class="fill" src="{{$widget['settings']['url']}}@if($widget['settings']['div_id'])#{{ $widget['settings']['div_id']}}@endif" @if ( ! $widget['settings']['pointer_events']) style="pointer-events: none" @endif>
    <p>Your browser does not support iframes.</p>
</iframe>
