<div id="count-{{ $widget->id }}" class="text-center" data-toggle="tooltip" data-placement="bottom" title="
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
  <h3 class="text-white drop-shadow truncate">
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
  <p class="text-white drop-shadow">
    @if ($widget->descriptor->category == 'facebook')
    {{ $widget->getDataManager()->getPage()->name }}
    @elseif ($widget->descriptor->category == 'google_analytics')
    {{ $widget->getDataManager()->getProperty()->name }}
    @elseif ($widget->descriptor->category == 'twitter')
    {{ SiteConstants::underscoreToCamelCase($widget->descriptor->category, TRUE) }} {{ $metric }}
    @endif
  </p>
</div>


@section('widgetScripts')
<script type="text/javascript">
  $(function() {
    
    $('#refresh-{{$widget->id}}').click(function () {
      refreshWidget({{ $widget->id }}, function (data) {
        $('#{{$widget->id}}-value').html(data.value);
      })
    });

    var toAlign = '#count-{{ $widget->id }}';
    var containerElement = '.gridster-player';

    verticalAlign(toAlign, containerElement);

    // Bind redraw to resize event.
    $(toAlign).bind('resize', function(e){
      verticalAlign(toAlign, containerElement);
    });

    function verticalAlign(target, measure) {
      $(target).css({
        'margin-top': $(target).closest(measure).outerHeight()/2-$(target).height()/2
      })
    }

   
    
    // $(toAlign).css({
    //     'position' : 'absolute',
    //     'left' : '50%',
    //     'top' : '50%',
    //     'margin-left' : -$(toAlign).closest('.gridster-player').outerWidth()/2,
    //     'margin-top' : -$(toAlign).closest('.gridster-player').outerHeight()/2
    // });

  });
</script>
@append