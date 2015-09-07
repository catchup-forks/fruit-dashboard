<li data-id='{{ $widget->id }}'
    data-row="{{ $widget->getPosition()->row }}"
    data-col="{{ $widget->getPosition()->col }}"
    data-sizex="{{ $widget->getPosition()->size_x }}"
    data-sizey="{{ $widget->getPosition()->size_y }}">

  <a class='deleteWidget' data-id='{{ $widget->id }}' href="">
    <span class="fa fa-times drop-shadow text-white color-hovered position-tr-sm display-hovered"></span>
  </a>

  <a href="{{ route('widget.edit', $widget->id) }}">
    <span class="fa fa-cog drop-shadow text-white color-hovered position-bl-sm display-hovered"></span>
  </a>

  @if ($widget instanceof iAjaxWidget)
  <a href="#" id="refresh-{{$widget->id}}" title="refresh widget content">
    <span class="fa fa-refresh position-tl-sm drop-shadow text-white color-hovered display-hovered"> </span>
  </a>
  @endif

  <!-- Adding loading on DataWidget -->
  @if (($widget->descriptor->is_premium) and (!Auth::user()->subscription->getSubscriptionInfo()['PE']))
      @include('widget.widget-trial-ended')
  @elseif ($widget->state == 'setup_required')
      @include('widget.widget-setup-required', ['widget' => $widget,])
  @else
    @if ($widget instanceof CronWidget)
      @include('widget.widget-loading', ['widget' => $widget,])
      <div class="@if ($widget->state == 'loading') not-visible @endif fill" id="widget-wrapper-{{$widget->id}}">
    @endif

    @include($widget->descriptor->getTemplateName(), ['widget' => $widget])

    <!-- Adding loading on DataWidget -->
    @if ($widget instanceof CronWidget)
      </div>
    @endif
  @endif

</li>