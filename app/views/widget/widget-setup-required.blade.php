<div class="text-white text-center drop-shadow margin-top-sm " id="widget-loading-{{ $widget->id }}">
    This widget is broken :( <br>
    You can try to reset it  <a href="{{ URL::route('widget.reset', $widget->id) }}">here</a>
</div>
