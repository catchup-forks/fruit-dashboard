<li data-id='{{ $widget->id }}'
    data-row="{{ $widget->getPosition()->row }}"
    data-col="{{ $widget->getPosition()->col }}"
    data-sizex="{{ $widget->getPosition()->size_x }}"
    data-sizey="{{ $widget->getPosition()->size_y }}">

  <a class='deleteWidget' data-id='{{ $widget->id }}' href="">
    <span class="fa fa-times drop-shadow text-white color-hovered position-tr-sm display-hovered"></span>
  </a>

  @if ($widget->getSettingsFields() != false)
  <a href="{{ route('widget.edit', $widget->id) }}">
    <span class="fa fa-cog drop-shadow text-white color-hovered position-bl-sm display-hovered"></span>
  </a>
  @endif
  
  @if ($widget instanceof iAjaxWidget)
  <span class="fa fa-refresh position-tl-sm drop-shadow text-white color-hovered display-hovered" id="refresh"></span>
  @endif

  @if ($widget->state == 'active')
    @include($widget->descriptor->getTemplateName(), [
      'widget' => $widget,
    ])
  @endif

</li>
