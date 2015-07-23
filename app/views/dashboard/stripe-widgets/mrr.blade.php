<h2 class="text-white text-center">
  <span>
    ${{$widget->getData()}}<br>
    Graph:
    @if ($widget->getSettings()['graph_on_off'])
      ON
    @else
      OFF
    @endif
  </span>
</h2>
