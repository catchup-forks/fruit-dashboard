{{-- here comes the wrapper for the layout chooser --}}

<div id="widget-layout-chooser-{{ $widget['id'] }}" class="layout-chooser display-hovered">
  <div class="element" data-layout="combined-bar-line"><i class="fa fa-bar-chart fa-fw text-white drop-shadow"></i></div> <!-- /.element -->
  <div class="element" data-layout="table"><i class="fa fa-table fa-fw text-white drop-shadow"></i></div> <!-- /.element -->
  <div class="element" data-layout="count"><i class="fa fa-database fa-fw text-white drop-shadow"></i></div> <!-- /.element -->
  <div class="element" data-layout="diff"><i class="fa fa-balance-scale fa-fw text-white drop-shadow"></i></div> <!-- /.element -->
</div> <!-- /.layout-chooser -->





@if ($widget['layout'] == 'table')
  @include('widget.widget-general-table')
@elseif ($widget['layout'] == 'count')
  @include('widget.widget-general-count')
@else
  @include('widget.widget-general-chart')
@endif
