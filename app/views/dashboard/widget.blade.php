@if (isset($widget['widget_ready']) && $widget['widget_ready'] == false)
  @include('dashboard.widget-notready', [
    'id' => $widget['widget_id']
   ])
@else

<li
  data-id='{{ $widget->id }}'
  data-row="{{ $widget->getPosition()->row }}"
  data-col="{{ $widget->getPosition()->col }}"
  data-sizex="{{ $widget->getPosition()->size_x }}"
  data-sizey="{{ $widget->getPosition()->size_y }}"

  <a href="">
    <span class="fa fa-times drop-shadow text-white color-hovered position-tr-sm display-hovered"></span>
  </a>

  <a href="{{route('widget.edit-settings', $widget->id)}}">
    <span class="fa fa-cog drop-shadow text-white color-hovered position-bl-sm display-hovered"></span>
  </a>

  @if ($widget->descriptor->type == 'clock')
    @include('dashboard.widget-clock', [
      'currentTime' => $widget['currentValue'],
     ])
  @endif

  @if ($widget->descriptor->type == 'greetings')
    @include('dashboard.widget-greetings', [
      'widget'   => $widget->getSpecific(),
      'position' => $widget['position']
    ])
  @endif

  @if ($widget->descriptor->type == 'quote')
    @include('dashboard.widget-quote', [
      'data' => $widget->getSpecific()->getData(),
      'position' => $widget['position']
    ])
  @endif
  @if ($widget->descriptor->type == 'stripe_mrr')
    @include('dashboard.stripe-widgets.mrr', [
      'widget' => $widget->getSpecific(),
      'position' => $widget['position']
    ])
  @endif

  @if ($widget['widget_type'] =='google-spreadsheet-text-cell')
    @include('dashboard.widget-text', [
      'text' => $widget['currentValue'],
      'id' => $widget['widget_id']
    ])
  @endif

  @if ($widget['widget_type'] =='google-spreadsheet-text-column')
    @include('dashboard.widget-list', [
      'list' => $widget['history'],
      'id' => $widget['widget_id']
    ])
  @endif

  @if ($widget['widget_type'] =='iframe')
    @include('dashboard.widget-iframe', [
      'iframeUrl' => json_decode($widget["currentValue"], true)['iframeURL'],
      'id' => $widget['widget_id']
    ])
  @endif

  @if ($widget['widget_type'] =='google-spreadsheet-text-column-random')
    @include('dashboard.widget-text', [
      'text' => $widget['currentValue'],
      'id' => $widget['widget_id']
    ])
  @endif

  @if ($widget['widget_type'] =='quote')
    @include('dashboard.widget-quote', [
      'quote' => json_decode($widget['currentValue'],true)['quote'],
      'author' => json_decode($widget['currentValue'],true)['author'],
      'id' => $widget['widget_id']
    ])
  @endif

  @if($widget['widget_type'] == 'note')
    @include('dashboard.widget-note', [
      'id' => $widget['widget_id'],
      'currentValue' => $widget['currentValue'],
      'position' => $widget['position']
    ])
  @endif

  @if($widget['widget_type'] == 'google-spreadsheet-line-column')
    @include('dashboard.widget-graph', [
      'id' => $widget['widget_id'],
      'position' => $widget['position']
    ])
  @endif

  @if ($widget['widget_type'] == 'text')
    @include('dashboard.widget-text', [
      'text' => $widget['currentValue'],
      'id' => $widget['widget_id']
     ])
  @endif

  </li>

@endif