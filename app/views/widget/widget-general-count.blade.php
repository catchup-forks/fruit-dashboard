<div data-toggle="tooltip" data-placement="bottom" title="
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

   is 
    <strong>
      {{ array_values($widget->getCurrentValue()['latest'])[0] }}
    </strong> 
    and it 

   <!-- Widget type related information -->
    has
    @if (array_values($widget->getCurrentValue()['diff'])[0] >= 0) increased @else decreased @endif
    by 
    <strong>{{ abs(array_values($widget->getCurrentValue()['diff'])[0]) }}
    </strong>

    since 
    <i>
      {{ $widget->getStartDate() }}
    </i>
    .
  ">
  <h3 class="text-white drop-shadow has-margin-horizontal text-center truncate">
    {{ array_values($widget->getCurrentValue()['latest'])[0] }}

    @if (array_values($widget->getCurrentValue()['diff'])[0] >= 0)
      <small class="text-success">
        <span class="fa fa-arrow-up"> </span>
    @else
      <small class="text-danger">
        <span class="fa fa-arrow-down"> </span>
    @endif
    
      {{ abs(array_values($widget->getCurrentValue()['diff'])[0]) }}
    </small>
  </h3>
  <p class="text-white drop-shadow has-margin-horizontal text-center">
    {{ $widget->getDataManager()->getPage()->name }}
    {{-- {{ SiteConstants::underscoreToCamelCase($widget->descriptor->category, TRUE) }} {{ $metric }} --}}
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