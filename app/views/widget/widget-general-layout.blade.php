<li data-id='{{ $widget->id }}'
    data-row="{{ $widget->getPosition()->row }}"
    data-col="{{ $widget->getPosition()->col }}"
    data-sizex="{{ $widget->getPosition()->size_x }}"
    data-sizey="{{ $widget->getPosition()->size_y }}"
    data-min-sizex="{{ $widget->descriptor->min_cols }}"
    data-min-sizey="{{ $widget->descriptor->min_rows }}"
    class="can-hover">

  <a class='deleteWidget' data-id='{{ $widget->id }}' data-hover="hover-unlocked" href="">
    <span class="fa fa-times drop-shadow text-white color-hovered position-tr-sm display-hovered"></span>
  </a>

  <a href="{{ route('widget.edit', $widget->id) }}" data-hover="hover-unlocked">
    <span class="fa fa-cog drop-shadow text-white color-hovered position-bl-sm display-hovered"></span>
  </a>

  @if ($widget instanceof iAjaxWidget)
  <a href="#" id="refresh-{{$widget->id}}" title="refresh widget content" data-hover="hover-unlocked">
    <span class="fa fa-refresh position-tl-sm drop-shadow text-white color-hovered display-hovered"> </span>
  </a>
  @endif

  <!-- Adding loading on DataWidget -->
  @if ($widget->state == 'setup_required')
      @include('widget.widget-setup-required', ['widget' => $widget,])
  @elseif ($widget instanceof SharedWidget)
    @include($widget->getRelatedWidget()->descriptor->getTemplateName(), ['widget' => $widget->getRelatedWidget()])
  @elseif ($widget->premiumUserCheck() === -1)
    @include('widget.widget-premium-not-allowed', ['feature' => 'hello'])
  @else
    @if ($widget instanceof iAjaxWidget)
      @include('widget.widget-loading', ['widget' => $widget,])
      <div class="@if ($widget->state == 'loading') not-visible @endif fill" id="widget-wrapper-{{$widget->id}}">
    @endif

    @include($widget->descriptor->getTemplateName(), ['widget' => $widget])
    <!-- Adding loading on DataWidget -->
    @if ($widget instanceof CronWidget)
      </div>
    @endif

  <div class="text-center">
    <a href="#" id="share-{{$widget->id}}" title="share widget data" data-hover="hover-unlocked" onclick="showShareModal({{$widget->id}})">
    share
    </a>
  </div>
  @endif
</li>