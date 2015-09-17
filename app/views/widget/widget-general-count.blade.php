  <div class="text-white drop-shadow has-margin-horizontal text-center" data-toggle="tooltip" data-placement="bottom" title="
   {{ SiteConstants::underscoreToCamelCase($widget->descriptor->category, TRUE) }}">
   <p>
   The number of {{ $metric }}
   <!-- Widget category related information -->
    @if ($widget->descriptor->category == 'facebook')
    on your page <i>{{ $widget->getDataManager()->getPage()->name }}</i>
    @elseif ($widget->descriptor->category == 'google_analytics')
     on your property <i>{{ $widget->getDataManager()->getProperty()->name }}</i>
    @elseif ($widget->descriptor->category == 'twitter')
    on <i>twitter</i>
    @endif
   <!-- ./Widget category related information -->

   <!-- Widget type related information -->
    has
    @if (array_values($widget->getCurrentValue())[0] >= 0) increased @else decreased @endif
    by

    <h3 id="{{$widget->id}}-value" class="truncate margin-top-sm">
      {{ abs(array_values($widget->getCurrentValue())[0]) }}
    </h3>
   <!-- Widget type related information -->

    since <!--<a href="{{ route('widget.edit', $widget->id) }}">--><i>{{ $widget->getStartDate() }}<!--</a>--></i>
    </p>
  </div>

@section('widgetScripts')
<script type="text/javascript">
  $(function() {
    $('#refresh-{{$widget->id}}').click(function () {
      refreshWidget({{ $widget->id }}, function (data) {
        $('#{{$widget->id}}-value').html(data.value);
      })
    })
  });
</script>
@append