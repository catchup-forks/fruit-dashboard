<div class="widget-inner text-center fill" id="widget-loading-{{ $widget->id }}">
  <p class="lead">This widget is broken :(</p>
  <p>You can try to reset it  <a href="{{ URL::route('widget.reset', $widget->id) }}">here</a>.</p>
</div>
