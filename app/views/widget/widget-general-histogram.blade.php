<div id="widget-layout-selector-{{ $widget['id'] }}" class="layout-chooser display-hovered">
  @foreach ($widget['possibleLayouts'] as $layout => $name)
    <div class="element @if ($layout==$widget['defaultLayout']) active @endif" data-layout="{{ $layout }}">
      <!-- FIXME FIX ICONS-->
      @if ($layout == SiteConstants::LAYOUT_SINGLE_LINE)
        <i class="fa fa-bar-chart fa-fw text-white drop-shadow"></i>
      @elseif ($layout == SiteConstants::LAYOUT_MULTI_LINE)
        <i class="fa fa-bar-chart fa-fw text-white drop-shadow"></i>
      @elseif ($layout == SiteConstants::LAYOUT_COMBINED_BAR_LINE)
        <i class="fa fa-bar-chart fa-fw text-white drop-shadow"></i>
      @elseif ($layout == SiteConstants::LAYOUT_TABLE)
        <i class="fa fa-table fa-fw text-white drop-shadow"></i>
      @elseif ($layout == SiteConstants::LAYOUT_COUNT)
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
  </div> <!-- /#widget-layout-wrapper-{{ $widget['id']}} -->
</div> <!-- /.flex-container -->

@section('widgetScripts')
<script type="text/javascript">
  // Set Widget default data
  var widgetData{{ $widget['id'] }} = {{ json_encode($widget['data']) }}
</script>
@append
