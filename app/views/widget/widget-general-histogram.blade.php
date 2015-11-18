<div id="widget-layout-selector-{{ $widget['id'] }}" class="layout-chooser display-hovered">
  @foreach ($widget['possibleLayouts'] as $layout => $name)
    <div class="element" data-layout="{{ $layout }}">
      <!-- FIXME FIX ICONS-->
      @if ($layout == 'chart')
        <i class="fa fa-bar-chart fa-fw text-white drop-shadow"></i>
      @elseif ($layout == 'combined_chart')
        <i class="fa fa-bar-chart fa-fw text-white drop-shadow"></i>
      @elseif ($layout == 'single-line')
        <i class="fa fa-bar-chart fa-fw text-white drop-shadow"></i>
      @elseif ($layout == 'multi-line')
        <i class="fa fa-bar-chart fa-fw text-white drop-shadow"></i>
      @elseif ($layout == 'combined-bar-line')
        <i class="fa fa-bar-chart fa-fw text-white drop-shadow"></i>
      @elseif ($layout == 'table')
        <i class="fa fa-table fa-fw text-white drop-shadow"></i>
      @elseif ($layout == 'count')
        <i class="fa fa-database fa-fw text-white drop-shadow"></i>
      @elseif ($layout == 'diff')
        <i class="fa fa-balance-scale fa-fw text-white drop-shadow">
      @endif
    </div> <!-- /.element -->
  @endforeach 
</div> <!-- /#widget-layout-selector-{{ $widget['id']}} -->

<div class="flex-container">
  <div id="widget-layouts-wrapper-{{ $widget['id'] }}">
    @foreach ($widget['possibleLayouts'] as $layout => $name)
      <div id="widget-layout-{{ $layout }}-{{ $widget['id'] }}" @if ($layout==$widget['defaultLayout']) class="active" @endif>
        @include('widget.widget-general-'.$layout, ['layout' => $layout])
      </div>
    @endforeach
  </div>
</div> <!-- /.widget-inner -->

@section('widgetScripts')
<script type="text/javascript">
  // Set Widget default data
  var widgetData{{ $widget['id'] }} = {{ json_encode($widget['data']) }}
</script>
@append
