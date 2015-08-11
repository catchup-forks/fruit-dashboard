<iframe class="fill" src="{{$widget->getSettings()['url']}}@if($widget->getSettings()['div_id'])#{{ $widget->getSettings()['div_id']}}@endif" @if ( ! $widget->getSettings()['pointer_events']) style="pointer-events: none" @endif>
    <p>Your browser does not support iframes.</p>
</iframe>
