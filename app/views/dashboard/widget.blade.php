@if (isset($widget_data['widget_ready']) && $widget_data['widget_ready'] == false)
  @include('dashboard.widget-notready', [
    'id' => $widget_data['widget_id']
   ])
@else

<li
  data-id='{{ $widget_data['widget_id'] }}' 
  data-row="{{ $widget_data['position']['row'] }}" 
  data-col="{{ $widget_data['position']['col'] }}" 
  data-sizex="{{ $widget_data['position']['x'] }}" 
  data-sizey="{{ $widget_data['position']['y'] }}">

  <a href="{{ URL::route('connect.deletewidget', $widget_data['widget_id']) }}">
    <span class="fa fa-times drop-shadow text-white color-hovered position-tr-sm display-hovered"></span>
  </a>

  {{-- uncomment for a settings cog --}}
  {{-- 
  <a href="#">
    <span class="fa fa-cog drop-shadow text-white color-hovered position-bl-sm display-hovered"></span>
  </a>
   --}}

  @if ($widget_data['widget_type'] == 'clock')
    @include('dashboard.widget-clock', [
      'currentTime' => $widget_data['currentValue'],
      'id' => $widget_data['widget_id']
     ])
  @endif

  @if ($widget_data['widget_type'] =='google-spreadsheet-text-cell')
    @include('dashboard.widget-text', [
      'text' => $widget_data['currentValue'], 
      'id' => $widget_data['widget_id']
    ])
  @endif

  @if ($widget_data['widget_type'] =='google-spreadsheet-text-column')
    @include('dashboard.widget-list', [
      'list' => $widget_data['history'], 
      'id' => $widget_data['widget_id']
    ])
  @endif

  @if ($widget_data['widget_type'] =='iframe')
    @include('dashboard.widget-iframe', [
      'iframeUrl' => json_decode($widget_data["currentValue"], true)['iframeURL'],
      'id' => $widget_data['widget_id']
    ])
  @endif

  @if ($widget_data['widget_type'] =='google-spreadsheet-text-column-random')
    @include('dashboard.widget-text', [
      'text' => $widget_data['currentValue'], 
      'id' => $widget_data['widget_id']
    ])
  @endif

  @if ($widget_data['widget_type'] =='quote')
    @include('dashboard.widget-quote', [
      'quote' => json_decode($widget_data['currentValue'],true)['quote'], 
      'author' => json_decode($widget_data['currentValue'],true)['author'], 
      'id' => $widget_data['widget_id']
    ])
  @endif

  @if($widget_data['widget_type'] == 'note')
    @include('dashboard.widget-note', [
      'id' => $widget_data['widget_id'],
      'currentValue' => $widget_data['currentValue'], 
      'position' => $widget_data['position']
    ])    
  @endif

  @if($widget_data['widget_type'] == 'greeting')
    @include('dashboard.widget-greeting', [
      'id' => $widget_data['widget_id'],
      'position' => $widget_data['position']
    ])
  @endif

  @if($widget_data['widget_type'] == 'google-spreadsheet-line-column')
    @include('dashboard.widget-graph', [
      'id' => $widget_data['widget_id'],
      'position' => $widget_data['position']
    ])
  @endif

  @if ($widget_data['widget_type'] == 'text')
    @include('dashboard.widget-text', [
      'text' => $widget_data['currentValue'], 
      'id' => $widget_data['widget_id']
     ])
  @endif

  </li>

@endif