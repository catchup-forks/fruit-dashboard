<div id="count-{{ $widget['id'] }}" class="flex-child text-center" data-toggle="tooltip" data-placement="bottom" title="

   {{ $widget['description'] }} is
    <strong>
      {{ array_values($widget['currentValue'])[0] }}
    </strong>
    and it

   <!-- Widget type related information -->
    has
    @if (array_values($widget['currentDiff'])[0] >= 0) increased @else decreased @endif
    by
    <strong>{{ abs(array_values($widget['currentDiff'])[0]) }}
    </strong>

    since
    <i>
      {{ $widget['startDate'] }}
    </i>
    .
  ">
  <h3 class="text-white drop-shadow truncate">
    {{ Utilities::formatNumber(array_values($widget['currentValue'])[0], $widget['format']) }}

    @if (array_values($widget['currentValue'])[0] >= 0)
      <small class="text-success">
        <span class="fa fa-arrow-up"> </span>
    @else
      <small class="text-danger">
        <span class="fa fa-arrow-down"> </span>
    @endif

      {{ abs(Utilities::formatNumber(array_values($widget['currentDiff'])[0], $widget['format'])) }}
    </small>
  </h3>
  <p class="text-white drop-shadow">
    {{ $widget['footer'] }}
  </p>
</div>


@section('widgetScripts')
@append
