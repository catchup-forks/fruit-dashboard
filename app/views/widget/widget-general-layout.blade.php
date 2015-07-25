@if (isset($widget['widget_ready']) && $widget['widget_ready'] == false)
  @include('widget.personal-widgets.widget-notready', [
    'id' => $widget['widget_id']
   ])
@else

<li data-id='{{ $widget->id }}'
    data-row="{{ $widget->getPosition()->row }}"
    data-col="{{ $widget->getPosition()->col }}"
    data-sizex="{{ $widget->getPosition()->size_x }}"
    data-sizey="{{ $widget->getPosition()->size_y }}">

  <a href="{{ route('widget.delete', $widget->id) }}">
    <span class="fa fa-times drop-shadow text-white color-hovered position-tr-sm display-hovered"></span>
  </a>

  <a href="{{ route('widget.edit', $widget->id) }}">
    <span class="fa fa-cog drop-shadow text-white color-hovered position-bl-sm display-hovered"></span>
  </a>

  {{-- PERSONAL | CLOCK --}}
  @if ($widget->descriptor->type == 'clock')
    @include('widget.personal-widgets.widget-clock', [
      'currentTime' => $widget['currentValue'],
     ])
  @endif

  {{-- PERSONAL | GREETINGS --}}
  @if ($widget->descriptor->type == 'greetings')
    @include('widget.personal-widgets.widget-greetings', [
      'widget'   => $widget->getSpecific(),
      'position' => $widget['position']
    ])
  @endif

  {{-- PERSONAL | QUOTE --}}
  @if ($widget->descriptor->type == 'quote')
    @include('widget.personal-widgets.widget-quote', [
      'data' => $widget->getSpecific()->getData(),
      'position' => $widget['position']
    ])
  @endif

  {{-- STRIPE | MRR --}}
  @if ($widget->descriptor->type == 'stripe_mrr')
    @include('widget.stripe-widgets.mrr', [
      'widget' => $widget->getSpecific(),
      'position' => $widget['position']
    ])
  @endif

</li>

@endif