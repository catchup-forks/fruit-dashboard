<h3>{{ Utilities::formatNumber($values[$resolution][$distance]['value'], $format) }}</h3>
<div class="@if ($values[$resolution][$distance]['success']) text-success @else text-danger @endif">
@if ($values[$resolution][$distance]['percent'] >= 0)
  <span class="fa fa-arrow-up"> </span>
@else
  <span class="fa fa-arrow-down"> </span>
@endif
{{-- compared to current value in percent --}}
{{ Utilities::formatNumber($values[$resolution][$distance]['percent'], '%.2f%%') }}
</div> <!-- /.text-success -->
<p><small>{{ $distance . " " . $resolution }} ago</small></p>